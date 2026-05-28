<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ChatBotService;
use Mockery;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChatBotTest extends TestCase
{
    public function test_guest_chatbot_message_endpoint_delegates_to_service(): void
    {
        $service = Mockery::mock(ChatBotService::class);
        $service->shouldReceive('sendMessage')
            ->once()
            ->with(null, 'guest-session-1', 'gợi ý sách kinh doanh')
            ->andReturn([
                'session_token' => 'guest-session-1',
                'session_title' => 'gợi ý sách kinh doanh',
                'messages' => [
                    ['sender_type' => 'user', 'message_text' => 'gợi ý sách kinh doanh'],
                    ['sender_type' => 'bot', 'message_text' => 'Mình gợi ý một số sách phù hợp.'],
                ],
                'reply' => 'Mình gợi ý một số sách phù hợp.',
                'suggestions' => [],
                'detected_intent' => 'recommendation',
            ]);

        $this->app->instance(ChatBotService::class, $service);

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
            'messages',
            'reply',
            'suggestions',
            'detected_intent',
        ]);
    }

    public function test_authenticated_user_session_endpoint_delegates_to_service(): void
    {
        $user = new User([
            'full_name' => 'Nguyen Van A',
            'email' => 'a@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);
        $user->setAttribute('id', 1);

        $service = Mockery::mock(ChatBotService::class);
        $service->shouldReceive('history')
            ->once()
            ->withArgs(function ($passedUser, $sessionToken) use ($user) {
                return $passedUser instanceof User
                    && $passedUser->email === $user->email
                    && $sessionToken === 'auth-session-1';
            })
            ->andReturn([
                'session_token' => 'auth-session-1',
                'session_title' => 'Tra cứu đơn',
                'messages' => [],
            ]);

        $this->app->instance(ChatBotService::class, $service);

        $this->actingAs($user)
            ->getJson(route('chatbot.session', [
                'session_token' => 'auth-session-1',
            ]))
            ->assertOk()
            ->assertJsonFragment([
                'session_token' => 'auth-session-1',
            ]);
    }
}
