<?php

namespace App\Services\ChatbotProviders;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class OpenAiProvider implements ProviderInterface
{
    public function reply(?User $user, string $message, array $context): array
    {
        $model = config('services.openai.model', 'gpt-4o-mini');
        $baseUri = rtrim((string) config('services.openai.base_uri', 'https://api.openai.com/v1'), '/');
        $apiKey = config('services.openai.key');
        if (! $apiKey) {
            throw new \RuntimeException('OpenAI API key not configured');
        }

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => config('chatbot.system_prompt')],
                ['role' => 'user', 'content' => $message],
            ],
            'temperature' => config('chatbot.openai.temperature', 0.6),
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
            'intent' => $context['intent'] ?? 'general',
            'suggestions' => [],
        ];
    }
}
