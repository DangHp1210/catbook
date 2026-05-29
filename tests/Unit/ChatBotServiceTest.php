<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\User;
use App\Services\ChatBotService;
use App\Services\ChatbotProviders\GeminiProvider;
use App\Services\ChatbotProviders\OpenAiProvider;
use App\Services\ChatbotProviders\ProviderFactory;
use App\Services\ChatbotProviders\ProviderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class ChatBotServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
        Http::preventStrayRequests();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_default_provider_success_returns_provider_response(): void
    {
        $service = $this->makeService(
            defaultProvider: $this->mockProvider([
                'text' => 'Mình gợi ý một số sách phù hợp.',
                'model_name' => 'gemini-test',
                'prompt_tokens' => 12,
                'completion_tokens' => 24,
                'intent' => 'recommendation',
                'suggestions' => [],
            ]),
            providerClasses: [GeminiProvider::class, OpenAiProvider::class],
        );

        $response = $service->sendMessage(null, 'guest-session-1', 'gợi ý sách kinh doanh');

        $this->assertSame('Mình gợi ý một số sách phù hợp.', $response['reply']);
        $this->assertSame('recommendation', $response['detected_intent']);
        $this->assertCount(2, $response['messages']);
        $this->assertSame('guest-session-1', $response['session_token']);
    }

    public function test_provider_fallback_chain_tries_next_provider(): void
    {
        $service = $this->makeService(
            defaultProvider: $this->mockProvider(throwable: new \RuntimeException('default provider failed')),
            fallbackProviders: [
                GeminiProvider::class => $this->mockProvider(throwable: new \RuntimeException('gemini failed')),
                OpenAiProvider::class => $this->mockProvider([
                    'text' => 'OpenAI đã trả lời.',
                    'model_name' => 'openai-test',
                    'prompt_tokens' => 5,
                    'completion_tokens' => 9,
                    'intent' => 'catalog_search',
                    'suggestions' => [],
                ]),
            ],
            providerClasses: [GeminiProvider::class, OpenAiProvider::class],
        );

        $response = $service->sendMessage(null, 'guest-session-2', 'tìm sách kinh doanh');

        $this->assertSame('OpenAI đã trả lời.', $response['reply']);
        $this->assertSame('catalog_search', $response['detected_intent']);
    }

    public function test_all_providers_fail_falls_back_to_rule_engine(): void
    {
        $service = $this->makeService(
            defaultProvider: $this->mockProvider(throwable: new \RuntimeException('default provider failed')),
            fallbackProviders: [
                GeminiProvider::class => $this->mockProvider(throwable: new \RuntimeException('gemini failed')),
                OpenAiProvider::class => $this->mockProvider(throwable: new \RuntimeException('openai failed')),
            ],
            providerClasses: [GeminiProvider::class, OpenAiProvider::class],
        );

        $response = $service->sendMessage(null, 'guest-session-3', 'xin chào');

        $this->assertSame('fallback-rule-engine', $this->latestChatLogModelName('guest-session-3'));
        $this->assertSame('fallback-rule-engine', $this->latestChatLogModelName('guest-session-3'));
        $this->assertStringContainsString('Mình có thể giúp bạn tìm sách', $response['reply']);
        $this->assertSame('catalog_search', $response['detected_intent']);
    }

    public function test_order_lookup_returns_order_details_when_providers_fail(): void
    {
        $user = User::query()->create([
            'full_name' => 'Nguyen Van A',
            'email' => 'a@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $orderCode = 'CB12345678901234567';
        Order::query()->create([
            'user_id' => $user->id,
            'order_code' => $orderCode,
            'recipient_name' => 'Nguyen Van A',
            'recipient_phone' => '0900000000',
            'shipping_address' => 'Ha Noi',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'order_status' => 'shipping',
            'subtotal' => 120000,
            'shipping_fee' => 20000,
            'discount_amount' => 0,
            'total_amount' => 140000,
            'note' => null,
        ]);

        $service = $this->makeService(
            defaultProvider: $this->mockProvider(throwable: new \RuntimeException('default provider failed')),
            fallbackProviders: [
                GeminiProvider::class => $this->mockProvider(throwable: new \RuntimeException('gemini failed')),
                OpenAiProvider::class => $this->mockProvider(throwable: new \RuntimeException('openai failed')),
            ],
            providerClasses: [GeminiProvider::class, OpenAiProvider::class],
        );

        $response = $service->sendMessage($user, 'order-session-1', "tra cứu đơn {$orderCode}");

        $this->assertStringContainsString($orderCode, $response['reply']);
        $this->assertStringContainsString('đang giao hàng', $response['reply']);
        $this->assertStringContainsString('đã thanh toán', $response['reply']);
        $this->assertSame('order_lookup', $response['detected_intent']);
    }

    public function test_category_query_only_returns_books_from_that_category_and_ranks_best_seller_first(): void
    {
        $user = User::query()->create([
            'full_name' => 'Nguyen Van B',
            'email' => 'b@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $literature = Category::query()->create([
            'name' => 'Văn học',
            'slug' => 'van-hoc',
        ]);

        $selfHelp = Category::query()->create([
            'name' => 'Self Help',
            'slug' => 'self-help',
        ]);

        $bestSeller = Book::query()->create([
            'title' => 'Tác phẩm Văn học 1',
            'slug' => 'tac-pham-van-hoc-1',
            'price' => 120000,
            'discount_price' => 110000,
            'stock_quantity' => 7,
            'status' => 'available',
        ]);
        $bestSeller->categories()->attach($literature->id);

        $otherLiterature = Book::query()->create([
            'title' => 'Tác phẩm Văn học 2',
            'slug' => 'tac-pham-van-hoc-2',
            'price' => 130000,
            'discount_price' => 125000,
            'stock_quantity' => 20,
            'status' => 'available',
        ]);
        $otherLiterature->categories()->attach($literature->id);

        $unrelated = Book::query()->create([
            'title' => 'Quản trị bản thân',
            'slug' => 'quan-tri-ban-than',
            'price' => 99000,
            'discount_price' => 89000,
            'stock_quantity' => 50,
            'status' => 'available',
        ]);
        $unrelated->categories()->attach($selfHelp->id);

        $this->createSoldOrder($user, $bestSeller, 5);
        $this->createSoldOrder($user, $otherLiterature, 1);
        $this->createSoldOrder($user, $unrelated, 10);

        $service = $this->makeService(
            defaultProvider: $this->mockProvider(throwable: new \RuntimeException('default provider failed')),
            fallbackProviders: [
                GeminiProvider::class => $this->mockProvider(throwable: new \RuntimeException('gemini failed')),
                OpenAiProvider::class => $this->mockProvider(throwable: new \RuntimeException('openai failed')),
            ],
            providerClasses: [GeminiProvider::class, OpenAiProvider::class],
        );

        $response = $service->sendMessage($user, 'category-session-1', 'gợi ý sách văn học');

        $this->assertSame('recommendation', $response['detected_intent']);
        $this->assertStringContainsString('Tác phẩm Văn học 1', $response['reply']);
        $this->assertStringContainsString('Tác phẩm Văn học 2', $response['reply']);
        $this->assertStringNotContainsString('Quản trị bản thân', $response['reply']);
        $this->assertLessThan(
            strpos($response['reply'], 'Tác phẩm Văn học 2'),
            strpos($response['reply'], 'Tác phẩm Văn học 1')
        );
    }

    public function test_price_query_parses_vietnamese_phrases_and_excludes_out_of_range_books(): void
    {
        $discountedCheap = Book::query()->create([
            'title' => 'Giảm giá đặc biệt',
            'slug' => 'giam-gia-dac-biet',
            'price' => 180000,
            'discount_price' => 85000,
            'stock_quantity' => 12,
            'status' => 'available',
        ]);

        $regularCheap = Book::query()->create([
            'title' => 'Sách giá tốt',
            'slug' => 'sach-gia-tot',
            'price' => 95000,
            'discount_price' => null,
            'stock_quantity' => 18,
            'status' => 'available',
        ]);

        $midRange = Book::query()->create([
            'title' => 'Sách tầm trung 200k',
            'slug' => 'sach-tam-trung-200k',
            'price' => 210000,
            'discount_price' => null,
            'stock_quantity' => 15,
            'status' => 'available',
        ]);

        $overBudget = Book::query()->create([
            'title' => 'Sách cao cấp',
            'slug' => 'sach-cao-cap',
            'price' => 350000,
            'discount_price' => null,
            'stock_quantity' => 9,
            'status' => 'available',
        ]);

        $service = $this->makeService(
            defaultProvider: $this->mockProvider(throwable: new \RuntimeException('default provider failed')),
            fallbackProviders: [
                GeminiProvider::class => $this->mockProvider(throwable: new \RuntimeException('gemini failed')),
                OpenAiProvider::class => $this->mockProvider(throwable: new \RuntimeException('openai failed')),
            ],
            providerClasses: [GeminiProvider::class, OpenAiProvider::class],
        );

        $underResponse = $service->sendMessage(null, 'price-session-under', 'sách dưới 100k');
        $aboveResponse = $service->sendMessage(null, 'price-session-above', 'sách 100k trở lên');
        $betweenResponse = $service->sendMessage(null, 'price-session-between', 'sách từ 100k đến 300k');
        $aroundResponse = $service->sendMessage(null, 'price-session-around', 'sách khoảng 200k');
        $cheapResponse = $service->sendMessage(null, 'price-session-cheap', 'sách giá rẻ');

        $this->assertStringContainsString('Giảm giá đặc biệt', $underResponse['reply']);
        $this->assertStringContainsString('Sách giá tốt', $underResponse['reply']);
        $this->assertStringNotContainsString('Sách tầm trung 200k', $underResponse['reply']);
        $this->assertStringNotContainsString('Sách cao cấp', $underResponse['reply']);

        $this->assertStringContainsString('Sách tầm trung 200k', $aboveResponse['reply']);
        $this->assertStringContainsString('Sách cao cấp', $aboveResponse['reply']);
        $this->assertStringNotContainsString('Giảm giá đặc biệt', $aboveResponse['reply']);
        $this->assertStringNotContainsString('Sách giá tốt', $aboveResponse['reply']);

        $this->assertStringContainsString('Sách tầm trung 200k', $betweenResponse['reply']);
        $this->assertStringNotContainsString('Giảm giá đặc biệt', $betweenResponse['reply']);
        $this->assertStringNotContainsString('Sách cao cấp', $betweenResponse['reply']);

        $this->assertStringContainsString('Sách tầm trung 200k', $aroundResponse['reply']);
        $this->assertStringNotContainsString('Sách cao cấp', $aroundResponse['reply']);

        $this->assertStringContainsString('Giảm giá đặc biệt', $cheapResponse['reply']);
        $this->assertStringContainsString('Sách giá tốt', $cheapResponse['reply']);
        $this->assertStringNotContainsString('Sách cao cấp', $cheapResponse['reply']);
    }

    private function makeService(
        mixed $defaultProvider,
        array $fallbackProviders = [],
        array $providerClasses = [GeminiProvider::class, OpenAiProvider::class],
    ): ChatBotService {
        $factory = Mockery::mock(ProviderFactory::class);
        $factory->shouldReceive('availableProviders')
            ->andReturn($providerClasses)
            ->byDefault();

        $this->app->instance(ProviderInterface::class, $defaultProvider);
        $this->app->instance(ProviderFactory::class, $factory);

        foreach ($fallbackProviders as $class => $provider) {
            $this->app->instance($class, $provider);
        }

        $this->app->forgetInstance(ChatBotService::class);

        return $this->app->make(ChatBotService::class);
    }

    private function mockProvider(?array $response = null, ?\Throwable $throwable = null): mixed
    {
        $provider = Mockery::mock(ProviderInterface::class);

        if ($throwable) {
            $provider->shouldReceive('reply')->andThrow($throwable);
        } else {
            $provider->shouldReceive('reply')->andReturn($response ?? []);
        }

        return $provider;
    }

    private function latestChatLogModelName(string $sessionToken): string
    {
        $session = \App\Models\ChatSession::query()->where('session_token', $sessionToken)->firstOrFail();
        $message = $session->messages()->latest('id')->firstOrFail();

        return $message->aiLog?->model_name ?? '';
    }

    private function createSoldOrder(User $user, Book $book, int $quantity): void
    {
        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_code' => 'CB'.str_pad((string) random_int(1, 99999999999999999), 17, '0', STR_PAD_LEFT),
            'recipient_name' => $user->full_name,
            'recipient_phone' => '0900000000',
            'shipping_address' => 'Ha Noi',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'order_status' => 'completed',
            'subtotal' => $book->price * $quantity,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'total_amount' => $book->price * $quantity,
            'note' => null,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'book_id' => $book->id,
            'book_title_snapshot' => $book->title,
            'unit_price' => $book->price,
            'quantity' => $quantity,
            'discount_amount' => 0,
            'total_price' => $book->price * $quantity,
        ]);
    }
}
