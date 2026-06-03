<?php

namespace App\Services\ChatbotProviders;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements ProviderInterface
{
    public function reply(?User $user, string $message, array $context): array
    {
        $apiKey = config('services.gemini.key');
        if (! $apiKey) {
            throw new \RuntimeException('Gemini API key not configured');
        }

        foreach ($this->candidateModels() as $model) {
            $response = Http::timeout(25)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                $this->payload($message, $context)
            );

            if (! $response->successful()) {
                $errorMessage = data_get($response->json(), 'error.message')
                    ?? data_get($response->json(), 'error.status')
                    ?? '';

                if ($response->status() === 404) {
                    continue;
                }

                if ($response->status() === 429) {
                    throw new \RuntimeException('Gemini request failed with status 429: ' . trim((string) $errorMessage));
                }

                throw new \RuntimeException(
                    'Gemini request failed with status ' . $response->status() . ($errorMessage !== '' ? ': ' . trim((string) $errorMessage) : '')
                );
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

        throw new \RuntimeException('Gemini request failed with status 404');
    }

    private function candidateModels(): array
    {
        $configured = trim((string) config('services.gemini.model', 'gemini-2.5-flash'));
        $fallbacks = [
            'gemini-1.5-flash',
        ];

        return array_values(array_unique(array_filter(array_merge([$configured], $fallbacks))));
    }

    private function payload(string $message, array $context): array
    {
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

        return $payload;
    }
}
