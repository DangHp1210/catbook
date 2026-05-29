<?php

namespace App\Services\ChatbotProviders;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements ProviderInterface
{
    public function reply(?User $user, string $message, array $context): array
    {
        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $apiKey = config('services.gemini.key');
        if (! $apiKey) {
            throw new \RuntimeException('Gemini API key not configured');
        }

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $payload = [
            'contents' => [[
                'role' => 'user',
                'parts' => [[
                    'text' => $message,
                ]],
            ]],
            'generationConfig' => [
                'temperature' => config('chatbot.gemini.temperature', 0.6),
                'maxOutputTokens' => config('chatbot.gemini.max_output_tokens', 500),
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
            'intent' => $context['intent'] ?? 'general',
            'suggestions' => [],
        ];
    }
}
