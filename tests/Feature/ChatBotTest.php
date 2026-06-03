<?php

namespace Tests\Feature;

use App\Models\ChatSession;
use App\Services\ChatbotProviders\GeminiProvider;
use App\Services\ChatbotProviders\ProviderFactory;
use App\Services\ChatbotProviders\ProviderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class ChatBotTest extends TestCase
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

    public function test_guest_session_message_endpoint_returns_expected_response_structure(): void
    {
        $defaultProvider = Mockery::mock(ProviderInterface::class);
        $defaultProvider->shouldReceive('reply')
            ->once()
            ->andReturn([
                'text' => 'Mình gợi ý một số sách phù hợp.',
                'model_name' => 'gemini-test',
                'prompt_tokens' => 10,
                'completion_tokens' => 20,
                'intent' => 'recommendation',
                'suggestions' => [],
            ]);

        $factory = Mockery::mock(ProviderFactory::class);
        $factory->shouldReceive('availableProviders')->andReturn([GeminiProvider::class]);

        $this->app->instance(ProviderInterface::class, $defaultProvider);
        $this->app->instance(ProviderFactory::class, $factory);
        $this->app->forgetInstance(\App\Services\ChatBotService::class);

        $response = $this->postJson(route('chatbot.message'), [
            'session_token' => 'guest-session-1',
            'message' => 'gợi ý sách kinh doanh',
        ]);

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('session_token', 'guest-session-1');
        $response->assertJsonStructure([
            'ok',
            'session_token',
            'session_title',
            'messages' => [
                '*' => [
                    'id',
                    'sender_type',
                    'message_text',
                    'message_type',
                    'created_at',
                    'related_book',
                ],
            ],
            'reply',
            'suggestions',
            'detected_intent',
        ]);

        $this->assertDatabaseHas('chat_sessions', [
            'session_token' => 'guest-session-1',
            'user_id' => null,
        ]);

        $this->assertCount(2, ChatSession::where('session_token', 'guest-session-1')->firstOrFail()->messages);
    }

    public function test_session_endpoint_returns_history_for_guest_session(): void
    {
        $defaultProvider = Mockery::mock(ProviderInterface::class);
        $defaultProvider->shouldReceive('reply')
            ->once()
            ->andReturn([
                'text' => 'Mình có thể giúp bạn tìm sách.',
                'model_name' => 'gemini-test',
                'prompt_tokens' => 1,
                'completion_tokens' => 1,
                'intent' => 'catalog_search',
                'suggestions' => [],
            ]);

        $factory = Mockery::mock(ProviderFactory::class);
        $factory->shouldReceive('availableProviders')->andReturn([GeminiProvider::class]);

        $this->app->instance(ProviderInterface::class, $defaultProvider);
        $this->app->instance(ProviderFactory::class, $factory);
        $this->app->forgetInstance(\App\Services\ChatBotService::class);

        $this->postJson(route('chatbot.message'), [
            'session_token' => 'guest-session-2',
            'message' => 'xin chào',
        ])->assertOk();

        $this->getJson(route('chatbot.session', ['session_token' => 'guest-session-2']))
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('session_token', 'guest-session-2')
            ->assertJsonStructure([
                'ok',
                'session_token',
                'session_title',
                'messages' => [
                    '*' => [
                        'id',
                        'sender_type',
                        'message_text',
                        'message_type',
                        'created_at',
                        'related_book',
                    ],
                ],
            ]);
    }
}
