<?php

namespace App\Services;

use App\Models\Book;
use App\Models\ChatAiLog;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class ChatBotService
{
    public function history(?User $user, ?string $sessionToken): array
    {
        $session = $this->resolveSession($user, $sessionToken);
        $messages = $this->loadMessages($session);

        return [
            'session_token' => $session->session_token,
            'session_title' => $session->title,
            'messages' => $messages,
        ];
    }

    public function sendMessage(?User $user, ?string $sessionToken, string $message): array
    {
        $cleanMessage = Str::squish($message);
        $session = $this->resolveSession($user, $sessionToken, $cleanMessage);

        $this->storeMessage($session, 'user', $cleanMessage);

        $startedAt = microtime(true);
        $context = $this->buildContext($user, $cleanMessage);

        try {
            $reply = $this->generateReply($user, $cleanMessage, $context);
            $modelName = $reply['model_name'];
        } catch (Throwable $throwable) {
            report($throwable);
            $reply = $this->fallbackReply($user, $cleanMessage, $context);
            $modelName = 'fallback-rule-engine';
        }

        $assistantMessage = $this->storeMessage($session, 'bot', $reply['text'], $reply['message_type'] ?? 'text', $reply['related_book_id'] ?? null);

        ChatAiLog::query()->create([
            'message_id' => $assistantMessage->id,
            'model_name' => $modelName,
            'prompt_tokens' => $reply['prompt_tokens'] ?? 0,
            'completion_tokens' => $reply['completion_tokens'] ?? 0,
            'total_tokens' => ($reply['prompt_tokens'] ?? 0) + ($reply['completion_tokens'] ?? 0),
            'response_time_ms' => (int) round((microtime(true) - $startedAt) * 1000),
        ]);

        $messages = $this->loadMessages($session);

        return [
            'session_token' => $session->session_token,
            'session_title' => $session->title,
            'messages' => $messages,
            'reply' => $reply['text'],
            'suggestions' => $reply['suggestions'] ?? [],
            'detected_intent' => $reply['intent'] ?? 'general',
        ];
    }

    private function resolveSession(?User $user, ?string $sessionToken, ?string $firstMessage = null): ChatSession
    {
        $token = trim((string) $sessionToken);
        if ($token === '') {
            $token = (string) Str::uuid();
        }

        $session = ChatSession::query()->firstOrCreate(
            ['session_token' => $token],
            [
                'user_id' => $user?->id,
                'title' => $firstMessage ? Str::limit($firstMessage, 48) : null,
            ]
        );

        if ($user && $session->user_id !== $user->id) {
            $session->forceFill(['user_id' => $user->id])->save();
        }

        if (! $session->title && $firstMessage) {
            $session->forceFill(['title' => Str::limit($firstMessage, 48)])->save();
        }

        return $session;
    }

    private function storeMessage(ChatSession $session, string $senderType, string $messageText, string $messageType = 'text', ?int $relatedBookId = null): ChatMessage
    {
        return $session->messages()->create([
            'sender_type' => $senderType,
            'message_text' => $messageText,
            'message_type' => $messageType,
            'related_book_id' => $relatedBookId,
        ]);
    }

    private function loadMessages(ChatSession $session): array
    {
        $messages = $session->messages()
            ->with(['relatedBook:id,title,slug,price,discount_price,stock_quantity'])
            ->with('aiLog')
            ->orderBy('id')
            ->limit(30)
            ->get();

        return $messages->map(fn (ChatMessage $message) => $this->formatMessage($message))->all();
    }

    private function formatMessage(ChatMessage $message): array
    {
        $createdAt = $message->created_at;
        $createdAtIso = null;

        if ($createdAt instanceof \DateTimeInterface) {
            $createdAtIso = $createdAt->format(DATE_ATOM);
        } elseif (is_string($createdAt) && $createdAt !== '') {
            $createdAtIso = (string) $createdAt;
        }

        return [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'message_text' => $message->message_text,
            'message_type' => $message->message_type,
            'created_at' => $createdAtIso,
            'related_book' => $message->relatedBook ? [
                'id' => $message->relatedBook->id,
                'title' => $message->relatedBook->title,
                'slug' => $message->relatedBook->slug,
                'price' => (float) $message->relatedBook->price,
                'discount_price' => $message->relatedBook->discount_price !== null ? (float) $message->relatedBook->discount_price : null,
                'stock_quantity' => (int) $message->relatedBook->stock_quantity,
            ] : null,
        ];
    }

    private function buildContext(?User $user, string $message): array
    {
        $orderCode = $this->extractOrderCode($message);
        $intent = $this->detectIntent($message);
        $books = $this->findBooksForQuery($message);
        $recommendations = $books->take(4)->values();
        $order = null;

        if ($orderCode !== null) {
            $order = $this->findOrder($user, $orderCode);
        }

        return [
            'order_code' => $orderCode,
            'intent' => $intent,
            'books' => $books,
            'recommendations' => $recommendations,
            'order' => $order,
        ];
    }

    private function detectIntent(string $message): string
    {
        $lower = Str::lower($message);

        if (preg_match('/\bCB\d{17}\b/', $message)) {
            return 'order_lookup';
        }

        if (Str::contains($lower, ['đơn hàng', 'don hang', 'tra cuu', 'tra cứu', 'order', 'mã đơn', 'ma don'])) {
            return 'order_lookup';
        }

        if (Str::contains($lower, ['gợi ý', 'goi y', 'recommend', 'nên đọc', 'nen doc', 'phù hợp', 'phu hop'])) {
            return 'recommendation';
        }

        if (Str::contains($lower, ['giá', 'gia', 'rẻ', 're', 'discount', 'giảm giá', 'giam gia'])) {
            return 'pricing';
        }

        if (Str::contains($lower, ['tồn kho', 'ton kho', 'còn hàng', 'con hang', 'hết hàng', 'het hang'])) {
            return 'stock';
        }

        return 'catalog_search';
    }

    private function extractOrderCode(string $message): ?string
    {
        if (preg_match('/\bCB\d{17}\b/', $message, $matches)) {
            return $matches[0];
        }

        return null;
    }

    private function findOrder(?User $user, string $orderCode): ?Order
    {
        $query = Order::query()
            ->with(['items.book:id,title,slug'])
            ->where('order_code', $orderCode);

        if ($user) {
            $query->where('user_id', $user->id);
        }

        return $query->first();
    }

    private function findBooksForQuery(string $message): EloquentCollection
    {
        $terms = collect(preg_split('/[^\pL\pN]+/u', Str::lower($message)) ?: [])
            ->map(fn ($term) => trim((string) $term))
            ->filter(fn ($term) => $term !== '' && mb_strlen($term) > 2)
            ->unique()
            ->values()
            ->take(6);

        $query = Book::query()
            ->with(['authors:id,name', 'categories:id,name'])
            ->where('status', 'available');

        if ($terms->isEmpty()) {
            return $query->latest()->limit(6)->get();
        }

        $query->where(function ($builder) use ($terms): void {
            foreach ($terms as $term) {
                $like = '%'.$term.'%';
                $builder->orWhere('title', 'like', $like)
                    ->orWhere('isbn', 'like', $like)
                    ->orWhereHas('authors', function ($authorQuery) use ($like): void {
                        $authorQuery->where('name', 'like', $like);
                    })
                    ->orWhereHas('categories', function ($categoryQuery) use ($like): void {
                        $categoryQuery->where('name', 'like', $like);
                    });
            }
        });

        return $query->latest()->limit(6)->get();
    }

    private function generateReply(?User $user, string $message, array $context): array
    {
        $apiKey = config('services.gemini.key') ?: config('services.openai.key');

        if ($apiKey) {
            return $this->providerReply($user, $message, $context);
        }

        return $this->fallbackReply($user, $message, $context);
    }

    private function providerReply(?User $user, string $message, array $context): array
    {
        if (config('services.gemini.key')) {
            return $this->geminiReply($user, $message, $context);
        }

        return $this->openAiReply($user, $message, $context);
    }

    private function geminiReply(?User $user, string $message, array $context): array
    {
        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $apiKey = config('services.gemini.key');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $payload = [
            'contents' => [[
                'role' => 'user',
                'parts' => [[
                    'text' => $this->buildPrompt($user, $message, $context),
                ]],
            ]],
            'generationConfig' => [
                'temperature' => 0.6,
                'maxOutputTokens' => 500,
            ],
        ];

        $response = Http::timeout(25)->post($endpoint.'?key='.$apiKey, $payload);

        if (! $response->successful()) {
            throw new \RuntimeException('Gemini request failed with status '.$response->status());
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
        $text = is_string($text) ? trim($text) : '';

        if ($text === '') {
            throw new \RuntimeException('Gemini returned an empty response');
        }

        return [
            'text' => $text,
            'model_name' => 'gemini:'.$model,
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
            'intent' => $context['intent'],
            'suggestions' => $this->buildSuggestions($context),
        ];
    }

    private function openAiReply(?User $user, string $message, array $context): array
    {
        $model = config('services.openai.model', 'gpt-4o-mini');
        $baseUri = rtrim((string) config('services.openai.base_uri', 'https://api.openai.com/v1'), '/');
        $apiKey = config('services.openai.key');

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $this->systemPrompt()],
                ['role' => 'user', 'content' => $this->buildPrompt($user, $message, $context)],
            ],
            'temperature' => 0.6,
        ];

        $response = Http::timeout(25)
            ->withToken($apiKey)
            ->post($baseUri.'/chat/completions', $payload);

        if (! $response->successful()) {
            throw new \RuntimeException('OpenAI request failed with status '.$response->status());
        }

        $text = data_get($response->json(), 'choices.0.message.content');
        $text = is_string($text) ? trim($text) : '';

        if ($text === '') {
            throw new \RuntimeException('OpenAI returned an empty response');
        }

        return [
            'text' => $text,
            'model_name' => 'openai:'.$model,
            'prompt_tokens' => (int) data_get($response->json(), 'usage.prompt_tokens', 0),
            'completion_tokens' => (int) data_get($response->json(), 'usage.completion_tokens', 0),
            'intent' => $context['intent'],
            'suggestions' => $this->buildSuggestions($context),
        ];
    }

    private function fallbackReply(?User $user, string $message, array $context): array
    {
        $intent = $context['intent'];
        $order = $context['order'];
        $books = $context['books'];
        $recommendations = $context['recommendations'];

        if ($intent === 'order_lookup') {
            if ($order) {
                $statusLabel = match ($order->order_status) {
                    'pending' => 'đang chờ xử lý',
                    'confirmed' => 'đã xác nhận',
                    'shipping' => 'đang giao hàng',
                    'completed' => 'đã hoàn tất',
                    'cancelled' => 'đã hủy',
                    'refunded' => 'đã hoàn tiền',
                    default => $order->order_status,
                };

                $paymentLabel = match ($order->payment_status) {
                    'unpaid' => 'chưa thanh toán',
                    'paid' => 'đã thanh toán',
                    'refunded' => 'đã hoàn tiền',
                    default => $order->payment_status,
                };

                return [
                    'text' => "Mình đã tra được đơn {$order->order_code}. Trạng thái hiện tại: {$statusLabel}. Thanh toán: {$paymentLabel}. Tổng tiền: ".number_format((float) $order->total_amount, 0, ',', '.')."đ. Bạn có thể xem chi tiết đơn trong trang đơn hàng của mình.",
                    'model_name' => 'fallback-rule-engine',
                    'intent' => $intent,
                    'suggestions' => $this->buildSuggestions($context),
                ];
            }

            return [
                'text' => 'Mình chưa tìm thấy đơn hàng đó. Nếu bạn muốn tra cứu, hãy gửi cho mình mã đơn bắt đầu bằng CB như CB20260528123456789. Nếu bạn đang dùng tài khoản khách, mình sẽ hỗ trợ tốt nhất khi có mã đơn chính xác.',
                'model_name' => 'fallback-rule-engine',
                'intent' => $intent,
                'suggestions' => $this->buildSuggestions($context),
            ];
        }

        if ($books->isNotEmpty()) {
            $lines = $books->take(4)->values()->map(function (Book $book, int $index): string {
                $price = (float) ($book->discount_price ?? $book->price);
                $authors = $book->authors->pluck('name')->filter()->implode(', ') ?: 'Đang cập nhật';

                return sprintf(
                    '%d. %s - %s - còn %d cuốn',
                    $index + 1,
                    $book->title,
                    number_format($price, 0, ',', '.') . 'đ',
                    (int) $book->stock_quantity
                );
            });

            $intro = match ($intent) {
                'recommendation' => 'Mình gợi ý một số sách phù hợp với nhu cầu của bạn:',
                'pricing' => 'Mình tìm được một số sách đúng với nhu cầu về giá:',
                'stock' => 'Mình đã kiểm tra tồn kho và thấy các sách sau còn hàng:',
                default => 'Mình đã tìm thấy một vài sách liên quan:',
            };

            return [
                'text' => $intro."\n".$lines->implode("\n")."\nNếu bạn muốn, mình có thể lọc tiếp theo thể loại, tác giả hoặc mức giá.",
                'model_name' => 'fallback-rule-engine',
                'intent' => $intent,
                'suggestions' => $this->buildSuggestions($context),
            ];
        }

        $defaultBooks = Book::query()
            ->with(['authors:id,name'])
            ->where('status', 'available')
            ->latest()
            ->limit(4)
            ->get();

        $fallbackLines = $defaultBooks->map(function (Book $book, int $index): string {
            $authors = $book->authors->pluck('name')->filter()->implode(', ') ?: 'Đang cập nhật';

            return sprintf(
                '%d. %s - %s - %s',
                $index + 1,
                $book->title,
                number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') . 'đ',
                $authors
            );
        })->implode("\n");

        return [
            'text' => "Mình có thể giúp bạn tìm sách theo tác giả, thể loại, mức giá hoặc tra cứu đơn hàng. Bạn thử mô tả nhu cầu ngắn gọn nhé, ví dụ: \"sách kinh doanh dưới 200k\" hoặc \"tra cứu đơn CB...\".\n\nMột vài sách mới để tham khảo:\n{$fallbackLines}",
            'model_name' => 'fallback-rule-engine',
            'intent' => $intent,
            'suggestions' => $this->buildSuggestions(['recommendations' => $defaultBooks, 'books' => $defaultBooks]),
        ];
    }

    private function buildPrompt(?User $user, string $message, array $context): string
    {
        $recommendations = $context['recommendations'];
        $order = $context['order'];

        $bookContext = $recommendations->map(function (Book $book): string {
            $authors = $book->authors->pluck('name')->filter()->implode(', ') ?: 'Đang cập nhật';
            $categories = $book->categories->pluck('name')->filter()->implode(', ') ?: 'Chưa phân loại';
            $price = number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') . 'đ';
            $stock = (int) $book->stock_quantity;

            return "- {$book->title} | Tác giả: {$authors} | Danh mục: {$categories} | Giá: {$price} | Tồn kho: {$stock}";
        })->implode("\n");

        $orderContext = $order ? sprintf(
            "- Mã đơn: %s | Trạng thái: %s | Thanh toán: %s | Tổng tiền: %sđ",
            $order->order_code,
            $order->order_status,
            $order->payment_status,
            number_format((float) $order->total_amount, 0, ',', '.')
        ) : '- Không có đơn hàng được tìm thấy';

        $userLabel = $this->userLabel($user);

        return trim(<<<PROMPT
    Bạn là trợ lý sách AI của CatBook — thân thiện, am hiểu sách và luôn trả lời bằng tiếng Việt.

    Nhiệm vụ chính:
    - Gợi ý sách phù hợp với nhu cầu của khách
    - Tóm tắt nội dung và đánh giá sách
    - Hỗ trợ tìm kiếm theo thể loại, tác giả, giá tiền

    Danh sách sách hiện có trong kho CatBook:
    {$bookContext}

Quy tắc:
    - Chỉ gợi ý sách có trong danh sách trên.
    - Luôn kèm link dạng /catalog/book/{slug} khi đề cập sách.
    - Trả lời ngắn gọn, thân thiện, không quá 300 từ.
    - Không bịa đặt thông tin sách hay tác giả.
    - Nếu không có đủ dữ liệu, hãy nói rõ là chưa tìm thấy trong kho CatBook.
    - Có thể trả lời về sách, tác giả, danh mục, giá, tồn kho và tra cứu đơn hàng cơ bản.
    - Nếu người dùng hỏi gợi ý sách, hãy ưu tiên các sách liên quan trong dữ liệu cung cấp.
    - Nếu có đơn hàng, ưu tiên trả lời trạng thái đơn và gợi ý bước tiếp theo.
    - Trình bày tự nhiên, có thể dùng gạch đầu dòng ngắn.

Thông tin người dùng:
- Trạng thái: {$userLabel}

Ngữ cảnh đơn hàng:
{$orderContext}

Sách liên quan:
{$bookContext}

Tin nhắn người dùng:
{$message}

Hãy trả lời như một shopping assistant, có thể đưa 2-4 gợi ý sách nếu phù hợp và mỗi sách phải đi kèm link catalog tương ứng.
PROMPT);
    }

    private function systemPrompt(): string
    {
        return 'Bạn là trợ lý sách AI của CatBook. Chỉ trả lời bằng tiếng Việt, chỉ dựa trên dữ liệu sách và đơn hàng được cung cấp, luôn ngắn gọn, thân thiện, không bịa đặt thông tin, và luôn kèm link /catalog/book/{slug} khi nhắc đến sách.';
    }

    private function buildSuggestions(array $context): array
    {
        $recommendations = collect($context['recommendations'] ?? $context['books'] ?? []);

        return $recommendations->take(4)->values()->map(function (Book $book): array {
            return [
                'title' => $book->title,
                'url' => route('catalog.book', $book->slug),
                'price' => (float) ($book->discount_price ?? $book->price),
                'stock_quantity' => (int) $book->stock_quantity,
            ];
        })->all();
    }

    private function userLabel(?User $user): string
    {
        if (! $user) {
            return 'Khách vãng lai';
        }

        return sprintf('%s (%s)', $user->full_name, $user->role);
    }
}
