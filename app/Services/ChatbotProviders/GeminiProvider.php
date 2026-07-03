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
            $baseMaxTokens = max(256, (int) config('chatbot.gemini.max_output_tokens', 900));
            $attemptTokens = [$baseMaxTokens, min($baseMaxTokens * 2, 2048)];

            foreach ($attemptTokens as $maxTokens) {
                $response = Http::timeout(25)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                    $this->payload($message, $context, $maxTokens)
                );

                if (! $response->successful()) {
                    $errorMessage = data_get($response->json(), 'error.message')
                        ?? data_get($response->json(), 'error.status')
                        ?? '';

                    if ($response->status() === 404) {
                        continue 2;
                    }

                    if ($response->status() === 429) {
                        throw new \RuntimeException('Gemini request failed with status 429: ' . trim((string) $errorMessage));
                    }

                    throw new \RuntimeException(
                        'Gemini request failed with status ' . $response->status() . ($errorMessage !== '' ? ': ' . trim((string) $errorMessage) : '')
                    );
                }

                $finishReason = (string) data_get($response->json(), 'candidates.0.finishReason', '');
                if ($finishReason === 'MAX_TOKENS') {
                    // Retry once with a larger output budget, still on Gemini.
                    continue;
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

            // If both attempts were truncated, return the second attempt instead of falling back.
            $response = Http::timeout(25)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                $this->payload($message, $context, min($baseMaxTokens * 2, 2048))
            );

            if ($response->successful()) {
                $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
                $text = is_string($text) ? trim($text) : '';

                if ($text !== '') {
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

    private function payload(string $message, array $context, ?int $maxOutputTokens = null): array
    {
        $history = collect($context['history'] ?? [])
            ->filter(fn ($turn): bool => is_array($turn) && isset($turn['role'], $turn['content']))
            ->map(function (array $turn): array {
                $role = $turn['role'] === 'assistant' ? 'model' : 'user';

                return [
                    'role' => $role,
                    'parts' => [[
                        'text' => (string) $turn['content'],
                    ]],
                ];
            })
            ->values()
            ->all();

        $contents = array_merge($history, [[
            'role' => 'user',
            'parts' => [[
                'text' => $message,
            ]],
        ]]);

        $payload = [
            'systemInstruction' => [
                'parts' => [[
                    'text' => (string) config('chatbot.system_prompt', ''),
                ]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => config('chatbot.gemini.temperature', 0.6),
                'maxOutputTokens' => $maxOutputTokens ?? (int) config('chatbot.gemini.max_output_tokens', 900),
            ],
        ];

        return $payload;
    }
}
