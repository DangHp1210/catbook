<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use App\Models\ChatAiLog;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Http;
use App\Services\ChatbotProviders\ProviderFactory;
use App\Services\ChatbotProviders\ProviderInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use Throwable;

class ChatBotService
{
    private ProviderInterface $defaultProvider;
    private ProviderFactory $providerFactory;
    private Container $container;

    public function __construct(ProviderInterface $defaultProvider, ProviderFactory $providerFactory, Container $container)
    {
        $this->defaultProvider = $defaultProvider;
        $this->providerFactory = $providerFactory;
        $this->container = $container;
    }

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

    public function clearHistory(?User $user, ?string $sessionToken): void
    {
        $token = trim((string) $sessionToken);

        if ($token === '') {
            return;
        }

        $query = ChatSession::query()->where('session_token', $token);

        if ($user) {
            $query->where(function ($builder) use ($user): void {
                $builder->whereNull('user_id')->orWhere('user_id', $user->id);
            });
        }

        $session = $query->first();

        if (! $session) {
            return;
        }

        $session->delete();
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
        $category = $this->detectCategory($message);
        $author = $this->detectAuthor($message);
        $priceRange = $this->extractPriceFilter($message);

        $books = $this->searchBooks($message, $category, $author, $priceRange);
        $recommendations = $books->take(4)->values();
        $order = null;

        if ($orderCode !== null) {
            $order = $this->findOrder($user, $orderCode);
        }

        return [
            'order_code' => $orderCode,
            'intent' => $intent,
            'category' => $category,
            'author' => $author,
            'price_range' => $priceRange,
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

    private function searchBooks(string $message, ?Category $category, ?Author $author, ?array $priceRange): EloquentCollection
    {
        $query = Book::query()
            ->with(['authors:id,name', 'categories:id,name'])
            ->where('status', 'available');

        if ($category) {
            return $this->searchByCategory($query, $category);
        }

        if ($author) {
            return $this->searchByAuthor($query, $author, $priceRange);
        }

        if ($priceRange !== null) {
            return $this->searchByPrice($query, $priceRange);
        }

        return $this->searchByKeyword($query, $message);
    }

    private function searchByCategory($query, Category $category): EloquentCollection
    {
        return $this->applyRanking(
            $query
            ->whereHas('categories', function ($categoryQuery) use ($category): void {
                $categoryQuery->whereKey($category->id);
            })
        );
    }

    private function searchByAuthor($query, Author $author, ?array $priceRange): EloquentCollection
    {
        if ($priceRange !== null) {
            $this->applyPriceRange($query, $priceRange);
        }

        return $this->applyRanking(
            $query
            ->whereHas('authors', function ($authorQuery) use ($author): void {
                $authorQuery->whereKey($author->id);
            })
        );
    }

    private function searchByPrice($query, array $priceRange): EloquentCollection
    {
        $this->applyPriceRange($query, $priceRange);

        return $this->applyRanking($query);
    }

    private function searchByKeyword($query, string $message): EloquentCollection
    {
        $terms = collect(preg_split('/[^\pL\pN]+/u', Str::lower($message)) ?: [])
            ->map(fn ($term) => trim((string) $term))
            ->filter(fn ($term) => $term !== '' && mb_strlen($term) > 2)
            ->unique()
            ->values()
            ->take(6);

        if ($terms->isEmpty()) {
            return $query
                ->withCount('orderItems')
                ->orderByDesc('order_items_count')
                ->orderByDesc('stock_quantity')
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
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

        return $this->applyRanking($query);
    }

    private function applyRanking($query): EloquentCollection
    {
        return $query
            ->withSum('orderItems', 'quantity')
            ->orderByDesc('order_items_sum_quantity')
            ->orderByDesc('stock_quantity')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
    }

    private function detectCategory(string $message): ?Category
    {
        $normalizedMessage = $this->normalizeText($message);

        $categories = Category::query()
            ->select(['id', 'name', 'slug'])
            ->get()
            ->sortByDesc(fn (Category $category): int => mb_strlen($this->normalizeText($category->name)))
            ->values();

        foreach ($categories as $category) {
            $categoryName = $this->normalizeText($category->name);
            $categorySlug = $this->normalizeText($category->slug);

            if ($categoryName !== '' && $this->containsWholePhrase($normalizedMessage, $categoryName)) {
                return $category;
            }

            if ($categorySlug !== '' && $this->containsWholePhrase($normalizedMessage, $categorySlug)) {
                return $category;
            }
        }

        return null;
    }

    private function detectAuthor(string $message): ?Author
    {
        $normalizedMessage = $this->normalizeText($message);

        $authors = Author::query()
            ->select(['id', 'name'])
            ->get()
            ->sortByDesc(fn (Author $author): int => mb_strlen($this->normalizeText($author->name)))
            ->values();

        foreach ($authors as $author) {
            $authorName = $this->normalizeText($author->name);

            if ($authorName !== '' && $this->containsWholePhrase($normalizedMessage, $authorName)) {
                return $author;
            }
        }

        return null;
    }

    private function extractPriceFilter(string $message): ?array
    {
        $normalized = $this->normalizeText($message);

        if (preg_match('/\b(?:gia re|re|cheap|budget)\b/u', $normalized)) {
            return ['min' => null, 'max' => 100000];
        }

        if (preg_match('/\b(?:tu|from)\s*(\d+[\d\.,]*)(k|nghin|ngan)?\s*(?:den|toi|to|and|-|~)\s*(\d+[\d\.,]*)(k|nghin|ngan)?\b/u', $normalized, $rangeMatches)) {
            return [
                'min' => $this->parsePriceAmount($rangeMatches[1], $rangeMatches[2] ?? null),
                'max' => $this->parsePriceAmount($rangeMatches[3], $rangeMatches[4] ?? null),
            ];
        }

        if (preg_match('/\b(?:khoang|gan|quanh|around|about)\s*(\d+[\d\.,]*)(k|nghin|ngan)?\b/u', $normalized, $exactMatches)) {
            $target = $this->parsePriceAmount($exactMatches[1], $exactMatches[2] ?? null);
            $delta = max(10000, (int) round($target * 0.1));

            return [
                'min' => max(0, $target - $delta),
                'max' => $target + $delta,
            ];
        }

        if (preg_match('/\b(?:duoi|duoi|duoi|toi da|max|under|less than)\s*(\d+[\d\.,]*)(k|nghin|ngan)?\b/u', $normalized, $matches)) {
            return [
                'min' => null,
                'max' => $this->parsePriceAmount($matches[1], $matches[2] ?? null),
            ];
        }

        if (preg_match('/\b(\d+[\d\.,]*)(k|nghin|ngan)?\s*(?:tro len|len tro|plus|or more|\+)\b/u', $normalized, $matches)) {
            return [
                'min' => $this->parsePriceAmount($matches[1], $matches[2] ?? null),
                'max' => null,
            ];
        }

        if (preg_match('/\b(?:tren|trên|tu|from|over|above)\s*(\d+[\d\.,]*)(k|nghin|ngan)?\b/u', $normalized, $matches)) {
            return [
                'min' => $this->parsePriceAmount($matches[1], $matches[2] ?? null),
                'max' => null,
            ];
        }

        return null;
    }

    private function applyPriceRange($query, array $priceRange): void
    {
        $query->where(function ($builder) use ($priceRange): void {
            $priceExpression = 'COALESCE(discount_price, price)';

            if (($priceRange['min'] ?? null) !== null) {
                $builder->whereRaw("{$priceExpression} >= ?", [(int) $priceRange['min']]);
            }

            if (($priceRange['max'] ?? null) !== null) {
                $builder->whereRaw("{$priceExpression} <= ?", [(int) $priceRange['max']]);
            }
        });
    }

    private function parsePriceValue(string $value): int
    {
        return (int) preg_replace('/[^0-9]/', '', $value);
    }

    private function parsePriceAmount(string $value, ?string $suffix = null): int
    {
        $amount = $this->parsePriceValue($value);
        $normalizedSuffix = Str::lower((string) $suffix);

        if ($normalizedSuffix !== '' || $amount < 1000) {
            return $amount * 1000;
        }

        return $amount;
    }

    private function normalizeText(string $value): string
    {
        $value = Str::of($value)->ascii()->lower()->toString();
        $value = preg_replace('/[^a-z0-9\s]+/u', ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }

    private function containsWholePhrase(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return false;
        }

        return preg_match('/(^|\s)'.preg_quote($needle, '/').'($|\s)/u', $haystack) === 1;
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
        $providers = $this->providerFactory->availableProviders();

        if (empty($providers)) {
            throw new \RuntimeException('No AI providers configured');
        }

        // Build a detailed prompt for provider (includes catalog and order context)
        $prompt = $this->buildPrompt($user, $message, $context);

        $lastException = null;

        // Try the default provider from the container first
        try {
            $reply = $this->defaultProvider->reply($user, $prompt, $context);
            $reply['suggestions'] = $reply['suggestions'] ?? $this->buildSuggestions($context);
            $reply['intent'] = $reply['intent'] ?? $context['intent'] ?? 'general';

            return $reply;
        } catch (\Throwable $e) {
            report($e);
            $lastException = $e;
            // continue to fallback providers
        }

        // Fallback chain: try providers from ProviderFactory in order, skipping the default provider class
        $defaultClass = get_class($this->defaultProvider);

        foreach ($providers as $providerClass) {
            // providerFactory returns class names; skip the default class to avoid retrying it
            if ($providerClass === $defaultClass) {
                continue;
            }

            try {
                $provider = $this->container->make($providerClass);
                if (! $provider instanceof ProviderInterface) {
                    continue;
                }

                $reply = $provider->reply($user, $prompt, $context);
                $reply['suggestions'] = $reply['suggestions'] ?? $this->buildSuggestions($context);
                $reply['intent'] = $reply['intent'] ?? $context['intent'] ?? 'general';

                return $reply;
            } catch (\Throwable $e) {
                report($e);
                $lastException = $e;
                continue;
            }
        }

        throw $lastException ?? new \RuntimeException('All providers failed');
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
        $category = $context['category'];
        $author = $context['author'];
        $priceRange = $context['price_range'];
        $categoryLine = $category ? 'Thể loại đã nhận diện: '.$category->name : 'Thể loại đã nhận diện: chưa có';
        $authorLine = $author ? 'Tác giả đã nhận diện: '.$author->name : 'Tác giả đã nhận diện: chưa có';
        $priceLine = 'Khoảng giá đã nhận diện: chưa có';

        if ($priceRange !== null) {
            $minText = $priceRange['min'] !== null ? number_format((int) $priceRange['min'], 0, ',', '.') : '...';
            $maxText = $priceRange['max'] !== null ? number_format((int) $priceRange['max'], 0, ',', '.') : '...';
            $priceLine = "Khoảng giá đã nhận diện: {$minText} - {$maxText}đ";
        }

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
{$this->systemPrompt()}

Mục tiêu trả lời:
- Đóng vai shopping assistant của CatBook, tập trung vào việc giúp user chọn sách nhanh hơn.
- Nếu có sách khớp, ưu tiên trả lời ngay bằng 2-4 lựa chọn tốt nhất.
- Nếu chưa có sách khớp, nói rõ lý do và gợi ý bộ lọc gần hơn.
- Nếu user hỏi đơn hàng, ưu tiên trả trạng thái đơn, thanh toán và bước tiếp theo.

Định dạng mong muốn:
- Mở đầu bằng câu kết luận ngắn 1 câu.
- Sau đó liệt kê các sách phù hợp theo dạng: tên sách - giá - tồn kho - link.
- Chỉ dùng thông tin nằm trong ngữ cảnh bên dưới.
- Không giải thích lan man, không nhắc tới dữ liệu không được cung cấp.

Thông tin người dùng:
- Trạng thái: {$userLabel}

{$categoryLine}
{$authorLine}
{$priceLine}

Ngữ cảnh đơn hàng:
{$orderContext}

Sách liên quan:
{$bookContext}

Tin nhắn người dùng:
{$message}

Yêu cầu cuối:
- Nếu nhắc đến sách nào, luôn kèm link /catalog/book/{slug}.
- Nếu phù hợp, có thể đề xuất thêm 1 câu hỏi ngắn để chốt nhu cầu của user.
PROMPT);
    }

    private function systemPrompt(): string
    {
        return (string) config('chatbot.system_prompt', 'Bạn là trợ lý mua sách của CatBook. Chỉ trả lời bằng tiếng Việt, chỉ dựa trên dữ liệu sách và đơn hàng được cung cấp, không bịa đặt thông tin, luôn tư vấn ngắn gọn như một shopping assistant.');
    }

    private function buildSuggestions(array $context): array
    {
        $recommendations = collect($context['recommendations'] ?? $context['books'] ?? []);

        return $recommendations->take(4)->values()->map(function (Book $book): array {
            return [
                'title' => $book->title,
                // return a relative path (e.g. /sach/{slug}) so frontend can display consistent catalog links
                'url' => parse_url(route('catalog.book', $book->slug), PHP_URL_PATH),
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
