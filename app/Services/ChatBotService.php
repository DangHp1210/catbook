<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\ChatAiLog;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Order;
use App\Models\User;
use App\Services\ChatbotProviders\ProviderFactory;
use App\Services\ChatbotProviders\ProviderInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

class ChatBotService
{
    private ProviderInterface $defaultProvider;
    private ProviderFactory   $providerFactory;
    private Container         $container;

    public function __construct(
        ProviderInterface $defaultProvider,
        ProviderFactory   $providerFactory,
        Container         $container
    ) {
        $this->defaultProvider = $defaultProvider;
        $this->providerFactory = $providerFactory;
        $this->container       = $container;
    }

    /* ══════════════════════════════════════════════════════
       PUBLIC API
    ══════════════════════════════════════════════════════ */

    public function history(?User $user, ?string $sessionToken): array
    {
        $session  = $this->resolveSession($user, $sessionToken);
        $messages = $this->loadMessages($session);

        return [
            'session_token' => $session->session_token,
            'session_title' => $session->title,
            'messages'      => $messages,
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
            $query->where(function ($b) use ($user): void {
                $b->whereNull('user_id')->orWhere('user_id', $user->id);
            });
        }

        $query->first()?->delete();
    }

    public function sendMessage(?User $user, ?string $sessionToken, string $message): array
    {
        $cleanMessage = Str::squish($message);
        $session      = $this->resolveSession($user, $sessionToken, $cleanMessage);

        $this->storeMessage($session, 'user', $cleanMessage);

        $startedAt = microtime(true);
        $context   = $this->buildContext($user, $cleanMessage, $session);

        try {
            $reply     = $this->generateReply($user, $cleanMessage, $context);
            $modelName = $reply['model_name'];
        } catch (Throwable $throwable) {
            if ($this->isGeminiQuotaError($throwable)) {
                Cache::put($this->geminiCooldownCacheKey(), now()->addMinutes(30)->timestamp, now()->addMinutes(30));
            }

            report($throwable);
            $reply     = $this->fallbackReply($user, $cleanMessage, $context);
            $modelName = 'fallback-rule-engine';
        }

        $reply = $this->enforceCatalogTruth($reply, $context);

        $reply['intent'] = $reply['intent'] ?? ($context['intent'] ?? 'general');
        $reply['text']   = $this->sanitizeReplyText($reply['text'] ?? '', (string) $reply['intent']);

        $assistantMessage = $this->storeMessage(
            $session,
            'bot',
            $reply['text'],
            $reply['message_type'] ?? 'text',
            $reply['related_book_id'] ?? null
        );

        ChatAiLog::query()->create([
            'message_id'        => $assistantMessage->id,
            'model_name'        => $modelName,
            'prompt_tokens'     => $reply['prompt_tokens'] ?? 0,
            'completion_tokens' => $reply['completion_tokens'] ?? 0,
            'total_tokens'      => ($reply['prompt_tokens'] ?? 0) + ($reply['completion_tokens'] ?? 0),
            'response_time_ms'  => (int) round((microtime(true) - $startedAt) * 1000),
        ]);

        return [
            'session_token'   => $session->session_token,
            'session_title'   => $session->title,
            'messages'        => $this->loadMessages($session),
            'reply'           => $reply['text'],
            'suggestions'     => $reply['suggestions'] ?? [],
            'detected_intent' => $reply['intent'] ?? 'general',
        ];
    }

    /* ══════════════════════════════════════════════════════
       SESSION
    ══════════════════════════════════════════════════════ */

    private function resolveSession(?User $user, ?string $sessionToken, ?string $firstMessage = null): ChatSession
    {
        $token = trim((string) $sessionToken);

        if ($user) {
            if ($token !== '') {
                $ownedSession = ChatSession::query()
                    ->where('session_token', $token)
                    ->where('user_id', $user->id)
                    ->first();

                if ($ownedSession) {
                    if (! $ownedSession->title && $firstMessage) {
                        $ownedSession->forceFill(['title' => Str::limit($firstMessage, 48)])->save();
                    }

                    return $ownedSession;
                }

                // Token exists but belongs to another account (or guest session),
                // isolate chat by issuing a fresh token for this user.
                $token = (string) Str::uuid();
            }

            if ($token === '') {
                $token = (string) Str::uuid();
            }

            return ChatSession::query()->firstOrCreate(
                [
                    'session_token' => $token,
                    'user_id'       => $user->id,
                ],
                [
                    'title' => $firstMessage ? Str::limit($firstMessage, 48) : null,
                ]
            );
        }

        if ($token === '') {
            $token = (string) Str::uuid();
        }

        $session = ChatSession::query()->firstOrCreate(
            [
                'session_token' => $token,
                'user_id'       => null,
            ],
            [
                'title'   => $firstMessage ? Str::limit($firstMessage, 48) : null,
            ]
        );

        if (! $session->title && $firstMessage) {
            $session->forceFill(['title' => Str::limit($firstMessage, 48)])->save();
        }

        return $session;
    }

    /* ══════════════════════════════════════════════════════
       MESSAGES
    ══════════════════════════════════════════════════════ */

    private function storeMessage(
        ChatSession $session,
        string $senderType,
        string $messageText,
        string $messageType = 'text',
        ?int $relatedBookId = null
    ): ChatMessage {
        return $session->messages()->create([
            'sender_type'     => $senderType,
            'message_text'    => $messageText,
            'message_type'    => $messageType,
            'related_book_id' => $relatedBookId,
        ]);
    }

    private function loadMessages(ChatSession $session): array
    {
        return $session->messages()
            ->with(['relatedBook:id,title,slug,price,discount_price,stock_quantity', 'aiLog'])
            ->orderBy('id')
            ->limit(30)
            ->get()
            ->map(fn (ChatMessage $m) => $this->formatMessage($m))
            ->all();
    }

    private function formatMessage(ChatMessage $message): array
    {
        $createdAt    = $message->created_at;
        $createdAtIso = null;

        if ($createdAt instanceof \DateTimeInterface) {
            $createdAtIso = $createdAt->format(DATE_ATOM);
        } elseif (is_string($createdAt) && $createdAt !== '') {
            $createdAtIso = $createdAt;
        }

        return [
            'id'           => $message->id,
            'sender_type'  => $message->sender_type,
            'message_text' => $message->message_text,
            'message_type' => $message->message_type,
            'created_at'   => $createdAtIso,
            'related_book' => $message->relatedBook ? [
                'id'             => $message->relatedBook->id,
                'title'          => $message->relatedBook->title,
                'slug'           => $message->relatedBook->slug,
                'price'          => (float) $message->relatedBook->price,
                'discount_price' => $message->relatedBook->discount_price !== null
                    ? (float) $message->relatedBook->discount_price
                    : null,
                'stock_status'   => $this->stockStatusLabel((int) $message->relatedBook->stock_quantity),
            ] : null,
        ];
    }

    /* ══════════════════════════════════════════════════════
       CONTEXT BUILDING
    ══════════════════════════════════════════════════════ */

    // [FIX C] Accept session to build chat history
    private function buildContext(?User $user, string $message, ChatSession $session): array
    {
        $orderCode  = $this->extractOrderCode($message);
        $intent     = $this->detectIntent($message);
        $category   = $this->detectCategory($message);
        $author     = $this->detectAuthor($message);
        $book       = $this->detectBook($message);
        $priceRange = $this->extractPriceFilter($message);
        $exactBook  = null;

        $books           = $this->searchBooks($message, $category, $author, $priceRange);

        if ($book) {
            $exactBook = Book::query()
                ->with(['authors:id,name', 'categories:id,name'])
                ->whereKey($book->id)
                ->first();

            if ($exactBook) {
                $books = new EloquentCollection(
                    $books
                        ->reject(fn (Book $b): bool => $b->id === $exactBook->id)
                        ->prepend($exactBook)
                        ->take(6)
                        ->values()
                        ->all()
                );
            }
        }

        $recommendations = $books->take(4)->values();

        if ($exactBook && $intent === 'summary') {
            $recommendations = new EloquentCollection([$exactBook]);
        }

        $order           = $orderCode ? $this->findOrder($user, $orderCode) : null;

        // Prefer deterministic rule-engine responses for explicit filters.
        if ($intent === 'catalog_search') {
            if ($priceRange !== null) {
                $intent = 'price_lookup';
            } elseif ($category) {
                $intent = 'category_lookup';
            } elseif ($author) {
                $intent = 'author_lookup';
            }
        }

        // Auto-upgrade intent when author detected
        if ($author && $intent === 'catalog_search') {
            $intent = 'recommendation';
        }

        return [
            'order_code'      => $orderCode,
            'intent'          => $intent,
            'category'        => $category,
            'author'          => $author,
            'price_range'     => $priceRange,
            'matched_book'    => $exactBook,
            'books'           => $books,
            'recommendations' => $recommendations,
            'order'           => $order,
            'chat_history'    => $this->buildChatHistory($session),
        ];
    }

    private function buildChatHistory(ChatSession $session): array
    {
        return $session->messages()
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->reverse()
            ->map(fn (ChatMessage $m) => [
                'role'    => $m->sender_type === 'user' ? 'user' : 'assistant',
                'content' => $m->message_text,
            ])
            ->values()
            ->all();
    }

    /* ══════════════════════════════════════════════════════
       INTENT / EXTRACT
    ══════════════════════════════════════════════════════ */

    private function detectIntent(string $message): string
    {
        $lower = Str::lower($message);

        if ($this->isGreetingOnlyMessage($message)) {
            return 'greeting';
        }

        if (preg_match('/\bCB\d{17}\b/', $message)) {
            return 'order_lookup';
        }

        if (Str::contains($lower, ['đơn hàng', 'don hang', 'tra cuu', 'tra cứu', 'order', 'mã đơn', 'ma don'])) {
            return 'order_lookup';
        }

        if (Str::contains($lower, ['gợi ý', 'goi y', 'recommend', 'nên đọc', 'nen doc', 'phù hợp', 'phu hop'])) {
            return 'recommendation';
        }

        if (Str::contains($lower, ['tóm tắt', 'tom tat', 'summarize', 'summary', 'tóm lược', 'tom luoc'])) {
            return 'summary';
        }

        if (Str::contains($lower, ['so sánh', 'so sanh', 'compare', 'comparison', 'khác nhau giữa', 'khac nhau giua'])) {
            return 'comparison';
        }

        if (Str::contains($lower, ['tư vấn', 'tu van', 'reading advice', 'nên đọc gì', 'nen doc gi', 'phù hợp với mình', 'phu hop voi minh'])) {
            return 'advice';
        }

        if (Str::contains($lower, ['tác giả', 'tac gia', 'author', 'kiểm tra tác giả', 'kiem tra tac gia'])) {
            return 'author_lookup';
        }

        if (Str::contains($lower, ['danh mục', 'danh muc', 'thể loại', 'the loai', 'category'])) {
            return 'category_lookup';
        }

        if (Str::contains($lower, ['giá', 'gia', 'rẻ', 're', 'discount', 'giảm giá', 'giam gia'])) {
            return 'price_lookup';
        }

        if (Str::contains($lower, ['tồn kho', 'ton kho', 'còn hàng', 'con hang', 'hết hàng', 'het hang'])) {
            return 'stock_lookup';
        }

        return 'catalog_search';
    }

    private function extractOrderCode(string $message): ?string
    {
        return preg_match('/\bCB\d{17}\b/', $message, $m) ? $m[0] : null;
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

    /* ══════════════════════════════════════════════════════
       BOOK SEARCH
    ══════════════════════════════════════════════════════ */

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
            $query->whereHas('categories', fn ($q) => $q->whereKey($category->id))
        );
    }

    private function searchByAuthor($query, Author $author, ?array $priceRange): EloquentCollection
    {
        if ($priceRange !== null) {
            $this->applyPriceRange($query, $priceRange);
        }

        return $this->applyRanking(
            $query->whereHas('authors', fn ($q) => $q->whereKey($author->id))
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
            ->map(fn ($t) => trim((string) $t))
            ->filter(fn ($t) => $t !== '' && mb_strlen($t) > 2)
            ->unique()->values()->take(6);

        if ($terms->isEmpty()) {
            return $query
                ->withCount('orderItems')
                ->orderByDesc('order_items_count')
                ->orderByDesc('stock_quantity')
                ->orderByDesc('created_at')
                ->limit(6)->get();
        }

        $query->where(function ($builder) use ($terms): void {
            foreach ($terms as $term) {
                $like = '%' . $term . '%';
                $builder
                    ->orWhere('title', 'like', $like)
                    ->orWhere('isbn', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('authors', fn ($q) => $q->where('name', 'like', $like))
                    ->orWhereHas('categories', fn ($q) => $q->where('name', 'like', $like));
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
            ->limit(6)->get();
    }

    /* ══════════════════════════════════════════════════════
       DETECT CATEGORY / AUTHOR / PRICE
    ══════════════════════════════════════════════════════ */

    private function detectCategory(string $message): ?Category
    {
        $normalized = $this->normalizeText($message);

        $categories = Category::query()->select(['id', 'name', 'slug'])->get()
            ->sortByDesc(fn (Category $c): int => mb_strlen($this->normalizeText($c->name)))
            ->values();

        foreach ($categories as $category) {
            $name = $this->normalizeText($category->name);
            $slug = $this->normalizeText($category->slug);

            if ($name !== '' && $this->containsWholePhrase($normalized, $name)) {
                return $category;
            }
            if ($slug !== '' && $this->containsWholePhrase($normalized, $slug)) {
                return $category;
            }
        }

        return null;
    }

    private function detectAuthor(string $message): ?Author
    {
        $normalized = $this->normalizeText($message);

        $authors = Author::query()->select(['id', 'name'])->get()
            ->sortByDesc(fn (Author $a): int => mb_strlen($this->normalizeText($a->name)))
            ->values();

        foreach ($authors as $author) {
            $name = $this->normalizeText($author->name);
            if ($name !== '' && $this->containsWholePhrase($normalized, $name)) {
                return $author;
            }
        }

        return null;
    }

    private function detectBook(string $message): ?Book
    {
        $normalized = $this->normalizeText($message);
        if ($normalized === '') {
            return null;
        }

        $books = Book::query()->select(['id', 'title', 'slug'])->get()
            ->sortByDesc(fn (Book $b): int => mb_strlen($this->normalizeText($b->title)))
            ->values();

        foreach ($books as $book) {
            $title = $this->normalizeText($book->title);
            $slug  = $this->normalizeText($book->slug);

            if ($title !== '' && $this->containsWholePhrase($normalized, $title)) {
                return $book;
            }

            if ($slug !== '' && $this->containsWholePhrase($normalized, $slug)) {
                return $book;
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

        if (preg_match('/\b(?:tu|from)\s*(\d+[\d.,]*)(k|nghin|ngan)?\s*(?:den|toi|to|and|-|~)\s*(\d+[\d.,]*)(k|nghin|ngan)?\b/u', $normalized, $m)) {
            return [
                'min' => $this->parsePriceAmount($m[1], $m[2] ?? null),
                'max' => $this->parsePriceAmount($m[3], $m[4] ?? null),
            ];
        }

        if (preg_match('/\b(?:khoang|gan|quanh|around|about)\s*(\d+[\d.,]*)(k|nghin|ngan)?\b/u', $normalized, $m)) {
            $target = $this->parsePriceAmount($m[1], $m[2] ?? null);
            $delta  = max(10000, (int) round($target * 0.1));
            return ['min' => max(0, $target - $delta), 'max' => $target + $delta];
        }

        if (preg_match('/\b(?:duoi|toi da|max|under|less than)\s*(\d+[\d.,]*)(k|nghin|ngan)?\b/u', $normalized, $m)) {
            return ['min' => null, 'max' => $this->parsePriceAmount($m[1], $m[2] ?? null)];
        }

        if (preg_match('/\b(\d+[\d.,]*)(k|nghin|ngan)?\s*(?:tro len|len tro|plus|or more|\+)\b/u', $normalized, $m)) {
            return ['min' => $this->parsePriceAmount($m[1], $m[2] ?? null), 'max' => null];
        }

        if (preg_match('/\b(?:tren|trên|tu|from|over|above)\s*(\d+[\d.,]*)(k|nghin|ngan)?\b/u', $normalized, $m)) {
            return ['min' => $this->parsePriceAmount($m[1], $m[2] ?? null), 'max' => null];
        }

        return null;
    }

    private function applyPriceRange($query, array $priceRange): void
    {
        $query->where(function ($builder) use ($priceRange): void {
            $expr = 'COALESCE(discount_price, price)';
            if (($priceRange['min'] ?? null) !== null) {
                $builder->whereRaw("{$expr} >= ?", [(int) $priceRange['min']]);
            }
            if (($priceRange['max'] ?? null) !== null) {
                $builder->whereRaw("{$expr} <= ?", [(int) $priceRange['max']]);
            }
        });
    }

    /* ══════════════════════════════════════════════════════
       AI REPLY GENERATION
    ══════════════════════════════════════════════════════ */

    private function generateReply(?User $user, string $message, array $context): array
    {
        if ($this->isRuleEngineIntent($context['intent'] ?? '')) {
            return $this->fallbackReply($user, $message, $context);
        }

        $hasKey = config('services.gemini.key') || config('services.openai.key');
        if ($hasKey && $this->isGeminiCoolingDown()) {
            return $this->fallbackReply($user, $message, $context);
        }

        return $hasKey
            ? $this->providerReply($user, $message, $context)
            : $this->fallbackReply($user, $message, $context);
    }

    private function isRuleEngineIntent(string $intent): bool
    {
        return in_array($intent, [
            'greeting',
            'order_lookup',
            'stock_lookup',
            'price_lookup',
            'author_lookup',
            'category_lookup',
        ], true);
    }

    private function providerReply(?User $user, string $message, array $context): array
    {
        $providers = $this->providerFactory->availableProviders();

        if (empty($providers)) {
            throw new \RuntimeException('No AI providers configured');
        }

        $prompt      = $this->buildPrompt($user, $message, $context);
        $chatHistory = $context['chat_history'] ?? [];

        $lastException = null;

        try {
            $reply = $this->defaultProvider->reply($user, $prompt, $context + [
                'history' => $chatHistory,
            ]);
            $reply['suggestions'] = $reply['suggestions'] ?? $this->buildSuggestions($context);
            $reply['intent']      = $reply['intent'] ?? ($context['intent'] ?? 'general');
            return $reply;
        } catch (Throwable $e) {
            report($e);
            $lastException = $e;
        }

        $defaultClass = get_class($this->defaultProvider);

        foreach ($providers as $providerClass) {
            if ($providerClass === $defaultClass) {
                continue;
            }

            try {
                $provider = $this->container->make($providerClass);
                if (! $provider instanceof ProviderInterface) {
                    continue;
                }

                $reply = $provider->reply($user, $prompt, $context + [
                    'history' => $chatHistory,
                ]);
                $reply['suggestions'] = $reply['suggestions'] ?? $this->buildSuggestions($context);
                $reply['intent']      = $reply['intent'] ?? ($context['intent'] ?? 'general');
                return $reply;
            } catch (Throwable $e) {
                report($e);
                $lastException = $e;
            }
        }

        throw $lastException ?? new \RuntimeException('All providers failed');
    }

    /* ══════════════════════════════════════════════════════
       FALLBACK (rule-based)
    ══════════════════════════════════════════════════════ */

    private function fallbackReply(?User $user, string $message, array $context): array
    {
        $intent          = $context['intent'];
        $order           = $context['order'];
        $books           = $context['books'];

        if ($intent === 'greeting') {
            return [
                'text'        => 'Xin chào! Mình là CatBook AI. Mình có thể hỗ trợ tìm sách, gợi ý sách phù hợp, hoặc tra cứu đơn hàng khi bạn cần.',
                'model_name'  => 'fallback-rule-engine',
                'intent'      => $intent,
                'suggestions' => [],
            ];
        }

        // If the user only sent a short greeting / chit-chat and provider failed,
        // return a friendly greeting instead of listing top books (avoids irrelevant replies).
        $normalizedMsg = $this->normalizeText($message);
        if ($intent === 'catalog_search' && preg_match('/\b(xin chao|xin chào|chao|chào|hello|hi)\b/u', $normalizedMsg)) {
            return [
                'text'       => 'Xin chào! Mình là CatBook AI. Mình có thể hỗ trợ tìm sách, gợi ý sách phù hợp, hoặc tra cứu đơn hàng khi bạn cần.',
                'model_name' => 'fallback-rule-engine',
                'intent'     => $intent,
                'suggestions'=> $this->buildSuggestions($context),
            ];
        }

        // Order lookup
        if ($intent === 'order_lookup') {
            if ($order) {
                $statusLabel = match ($order->order_status) {
                    'pending'   => 'đang chờ xử lý',
                    'confirmed' => 'đã xác nhận',
                    'shipping'  => 'đang giao hàng',
                    'completed' => 'đã hoàn tất',
                    'cancelled' => 'đã hủy',
                    'refunded'  => 'đã hoàn tiền',
                    default     => $order->order_status,
                };
                $paymentLabel = match ($order->payment_status) {
                    'unpaid'   => 'chưa thanh toán',
                    'paid'     => 'đã thanh toán',
                    'refunded' => 'đã hoàn tiền',
                    default    => $order->payment_status,
                };

                return [
                    'text'       => "Đơn {$order->order_code} — trạng thái: {$statusLabel}, thanh toán: {$paymentLabel}, tổng: " . number_format((float) $order->total_amount, 0, ',', '.') . "đ.",
                    'model_name' => 'fallback-rule-engine',
                    'intent'     => $intent,
                    'suggestions'=> [],
                ];
            }

            return [
                'text'       => 'Chưa tìm thấy đơn hàng đó. Hãy gửi mã đơn bắt đầu bằng CB (ví dụ: CB20260528123456789).',
                'model_name' => 'fallback-rule-engine',
                'intent'     => $intent,
                'suggestions'=> [],
            ];
        }

        // Books found
        if ($books->isNotEmpty()) {
            $lines = $books->take(4)->values()->map(function (Book $book, int $index): string {
                $price       = number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') . 'đ';
                $authors     = $book->authors->pluck('name')->filter()->implode(', ') ?: 'Đang cập nhật';
                $stockStatus = $this->stockStatusLabel((int) $book->stock_quantity);

                return sprintf('%d. %s — %s — %s — %s', $index + 1, $book->title, $authors, $price, $stockStatus);
            });

            $intro = match ($intent) {
                'recommendation' => 'Gợi ý phù hợp nhất:',
                'summary'        => 'Tóm tắt nhanh:',
                'comparison'     => 'So sánh nhanh:',
                'advice'         => 'Tư vấn đọc sách:',
                'price_lookup'   => 'Sách theo mức giá bạn cần:',
                'stock_lookup'   => 'Sách đang còn hàng:',
                'author_lookup'  => 'Sách theo tác giả bạn tìm:',
                'category_lookup'=> 'Sách trong danh mục bạn hỏi:',
                default          => 'Kết quả tìm kiếm:',
            };

            return [
                'text'       => $intro . "\n" . $lines->implode("\n"),
                'model_name' => 'fallback-rule-engine',
                'intent'     => $intent,
                'suggestions'=> $this->buildSuggestions($context),
            ];
        }

        // Default: new books
        $defaultBooks = Book::query()
            ->with(['authors:id,name'])
            ->where('status', 'available')
            ->latest()->limit(4)->get();

        $fallbackLines = $defaultBooks->map(fn (Book $book, int $index): string => sprintf(
            '%d. %s — %s — %sđ',
            $index + 1,
            $book->title,
            $book->authors->pluck('name')->filter()->implode(', ') ?: 'Đang cập nhật',
            number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.')
        ))->implode("\n");

        return [
            'text'       => "Chưa tìm thấy sách khớp tiêu chí trong kho CatBook. Một vài sách gần nhất:\n{$fallbackLines}",
            'model_name' => 'fallback-rule-engine',
            'intent'     => 'recommendation',
            'suggestions'=> $this->buildSuggestions(['recommendations' => $defaultBooks, 'books' => $defaultBooks]),
        ];
    }

    /* ══════════════════════════════════════════════════════
       PROMPT BUILDING
    ══════════════════════════════════════════════════════ */

    private function buildPrompt(?User $user, string $message, array $context): string
    {
        $recommendations = $context['recommendations'];
        $order           = $context['order'];
        $category        = $context['category'];
        $author          = $context['author'];
        $priceRange      = $context['price_range'];

        $categoryLine = $category   ? "Thể loại: {$category->name}"    : 'Thể loại: chưa nhận diện';
        $authorLine   = $author     ? "Tác giả: {$author->name}"        : 'Tác giả: chưa nhận diện';
        $priceLine    = 'Giá: chưa nhận diện';

        if ($priceRange !== null) {
            $min       = $priceRange['min'] !== null ? number_format((int) $priceRange['min'], 0, ',', '.') . 'đ' : '...';
            $max       = $priceRange['max'] !== null ? number_format((int) $priceRange['max'], 0, ',', '.') . 'đ' : '...';
            $priceLine = "Giá: {$min} – {$max}";
        }

        // [FIX C] Include description in book context
        $bookContext = $recommendations->map(function (Book $book): string {
            $authors    = $book->authors->pluck('name')->filter()->implode(', ') ?: 'Đang cập nhật';
            $categories = $book->categories->pluck('name')->filter()->implode(', ')  ?: 'Chưa phân loại';
            $price      = number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') . 'đ';
            $stock      = $this->stockStatusLabel((int) $book->stock_quantity);
            $slug       = $book->slug;

            $line = "• {$book->title} | {$authors} | {$categories} | {$price} | {$stock}";

            // Inject short description if available
            if (! empty($book->description)) {
                $desc = Str::limit(strip_tags($book->description), 100);
                $line .= "\n  Mô tả: {$desc}";
            }

            return $line;
        })->implode("\n");

        $orderContext = $order
            ? "• {$order->order_code} | {$order->order_status} | {$order->payment_status} | " . number_format((float) $order->total_amount, 0, ',', '.') . 'đ'
            : '• Không tìm thấy đơn hàng';

        // [FIX B] NOTE: systemPrompt() is NOT injected here — provider handles it separately
        // so it doesn't get duplicated in the final API call
        return trim(<<<PROMPT
=== BỘ LỌC ===
{$categoryLine}
{$authorLine}
{$priceLine}
Người dùng: {$this->userLabel($user)}

=== SÁCH LIÊN QUAN ===
{$bookContext}

=== ĐƠN HÀNG ===
{$orderContext}

=== CÂU HỎI ===
{$message}

=== QUY TẮC BẮT BUỘC TRẢ LỜI ===
- Trả lời đúng trọng tâm câu hỏi hiện tại, không lan man, không thêm thông tin ngoài yêu cầu.
- Không mở đầu xã giao dài dòng (không dùng các câu như "Chào bạn ..., dưới đây là...") trừ khi user chỉ chào.
- Không kết thúc bằng câu hỏi ngược hoặc câu mời lọc thêm.
- Tối đa 3 câu ngắn hoặc 4 gạch đầu dòng ngắn.
- Không kèm link trong nội dung trả lời.
PROMPT);
    }

    // [FIX D] Improved system prompt with few-shot examples and clear format
    private function systemPrompt(): string
    {
        return (string) config('chatbot.system_prompt',
            'Bạn là trợ lý mua sách của CatBook. Chỉ trả lời bằng tiếng Việt, chỉ dựa trên dữ liệu sách và đơn hàng được cung cấp, không bịa đặt thông tin. Gemini dùng cho gợi ý sách theo nhu cầu, trả lời tự nhiên, tóm tắt sách, so sánh sách và tư vấn đọc sách. Rule engine dùng cho tra cứu đơn hàng, kiểm tra tồn kho, kiểm tra giá, kiểm tra tác giả, kiểm tra danh mục và dữ liệu lấy trực tiếp từ database. Khi có dữ liệu sách trong ngữ cảnh, phải nêu ngay tên sách cụ thể, tác giả, giá và tình trạng; khi là đơn hàng thì nêu mã đơn, trạng thái đơn, thanh toán và bước tiếp theo. Không mở đầu bằng lời chào dài hoặc câu lấp lửng như "Dựa trên cuốn sách...". Không hiển thị slug hoặc đường dẫn /catalog/book/{slug} trong phần trả lời; link chỉ dùng ở suggestions. Mỗi câu trả lời phải đủ thông tin ngay từ lượt đầu, không đẩy việc lọc sang câu hỏi tiếp theo. Không hiển thị số lượng tồn kho chính xác; chỉ dùng các trạng thái Còn hàng, Sắp hết hàng, Hết hàng.'
        );
    }

    /* ══════════════════════════════════════════════════════
       HELPERS
    ══════════════════════════════════════════════════════ */

    private function sanitizeReplyText(string $text, string $intent = 'general'): string
    {
        // Remove lightweight markdown markers from provider output for cleaner UI text bubbles.
        $text = preg_replace('/\*\*(.*?)\*\*/u', '$1', $text) ?? $text;
        $text = preg_replace('/__(.*?)__/u', '$1', $text) ?? $text;
        $text = preg_replace('~\s*[-–—]?\s*/catalog/book/[^\s<>"]+~iu', '', $text) ?? $text;
        $text = preg_replace_callback('/\b(?:Tồn kho|tồn kho|Kho|kho)\s*:\s*(\d+)\b/u', function (array $m): string {
            return 'Tình trạng: ' . $this->stockStatusLabel((int) $m[1]);
        }, $text) ?? $text;
        $text = preg_replace_callback('/\b(?:còn|con)\s+(\d+)\s+(?:cuốn|quyển|sách)\b/iu', function (array $m): string {
            return $this->stockStatusLabel((int) $m[1]);
        }, $text) ?? $text;
        $text = preg_replace('/[ \t]+$/mu', '', $text) ?? $text;
        $text = preg_replace('/\n{3,}/u', "\n\n", $text) ?? $text;
        $text = trim($text);

        if ($intent !== 'greeting') {
            $text = preg_replace('/\n?\s*(Ban|Bạn)\s+mu[oố]n[^\n\r?]*\?\s*$/iu', '', $text) ?? $text;
            $text = preg_replace('/\n?\s*(C[ầa]n|Mu[oố]n)\s+m[iì]nh[^\n\r?]*\?\s*$/iu', '', $text) ?? $text;
            $text = preg_replace('/\s+$/u', '', $text) ?? $text;
        }

        return trim($text);
    }

    private function enforceCatalogTruth(array $reply, array $context): array
    {
        $matchedBook = $context['matched_book'] ?? null;
        if (! $matchedBook instanceof Book) {
            return $reply;
        }

        $rawText = (string) ($reply['text'] ?? '');
        $normalized = $this->normalizeText($rawText);

        $denialPhrases = [
            'khong co trong kho',
            'khong co trong kho du lieu',
            'chua tim thay sach',
            'khong tim thay sach',
            'khong co san',
        ];

        $hasDenial = false;
        foreach ($denialPhrases as $phrase) {
            if (str_contains($normalized, $phrase)) {
                $hasDenial = true;
                break;
            }
        }

        if (! $hasDenial) {
            return $reply;
        }

        $title       = $matchedBook->title;
        $authors     = $matchedBook->authors->pluck('name')->filter()->implode(', ') ?: 'Đang cập nhật';
        $price       = number_format((float) ($matchedBook->discount_price ?? $matchedBook->price), 0, ',', '.') . 'đ';
        $stockStatus = $this->stockStatusLabel((int) $matchedBook->stock_quantity);
        $summary     = trim((string) Str::limit(strip_tags((string) $matchedBook->description), 260));

        $reply['text'] = $summary !== ''
            ? "CatBook có sẵn \"{$title}\" của {$authors}. Giá: {$price}, tình trạng: {$stockStatus}. Tóm tắt ngắn: {$summary}"
            : "CatBook có sẵn \"{$title}\" của {$authors}. Giá: {$price}, tình trạng: {$stockStatus}.";

        $reply['related_book_id'] = $matchedBook->id;
        $reply['suggestions'] = $this->buildSuggestions([
            'recommendations' => new EloquentCollection([$matchedBook]),
            'books' => new EloquentCollection([$matchedBook]),
        ]);

        return $reply;
    }

    private function stockStatusLabel(int $stockQuantity): string
    {
        if ($stockQuantity <= 0) {
            return 'Hết hàng';
        }
        if ($stockQuantity <= 5) {
            return 'Sắp hết hàng';
        }
        return 'Còn hàng';
    }

    private function buildSuggestions(array $context): array
    {
        return collect($context['recommendations'] ?? $context['books'] ?? [])
            ->take(4)->values()
            ->map(fn (Book $book): array => [
                'title'        => $book->title,
                'url'          => parse_url(route('catalog.book', $book->slug), PHP_URL_PATH),
                'price'        => (float) ($book->discount_price ?? $book->price),
                'stock_status' => $this->stockStatusLabel((int) $book->stock_quantity),
            ])
            ->all();
    }

    private function userLabel(?User $user): string
    {
        return $user ? "{$user->full_name} ({$user->role})" : 'Khách vãng lai';
    }

    private function parsePriceValue(string $value): int
    {
        return (int) preg_replace('/[^0-9]/', '', $value);
    }

    private function parsePriceAmount(string $value, ?string $suffix = null): int
    {
        $amount           = $this->parsePriceValue($value);
        $normalizedSuffix = Str::lower((string) $suffix);

        return ($normalizedSuffix !== '' || $amount < 1000)
            ? $amount * 1000
            : $amount;
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
        return preg_match('/(^|\s)' . preg_quote($needle, '/') . '($|\s)/u', $haystack) === 1;
    }

    private function isGreetingOnlyMessage(string $message): bool
    {
        $normalized = $this->normalizeText($message);

        if ($normalized === '') {
            return false;
        }

        return preg_match(
            '/^(xin chao|chao|hello|hi|alo|hey|good morning|good afternoon|good evening)( ban| shop| catbook)?$/u',
            $normalized
        ) === 1;
    }

    private function geminiCooldownCacheKey(): string
    {
        return 'chatbot.gemini.cooldown_until';
    }

    private function isGeminiCoolingDown(): bool
    {
        $cooldownUntil = (int) Cache::get($this->geminiCooldownCacheKey(), 0);
        return $cooldownUntil > now()->timestamp;
    }

    private function isGeminiQuotaError(Throwable $throwable): bool
    {
        $message = $throwable->getMessage();

        return str_contains($message, 'status 429')
            || str_contains($message, 'RESOURCE_EXHAUSTED')
            || str_contains($message, 'Quota exceeded');
    }
}