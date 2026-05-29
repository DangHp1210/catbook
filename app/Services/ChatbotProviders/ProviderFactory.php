<?php

namespace App\Services\ChatbotProviders;

class ProviderFactory
{
    public function availableProviders(): array
    {
        $mapping = [
            'gemini' => GeminiProvider::class,
            'openai' => OpenAiProvider::class,
        ];

        $available = [];
        foreach ($mapping as $key => $class) {
            if (config("services.{$key}.key")) {
                $available[$key] = $class;
            }
        }

        $default = config('chatbot.default_provider');

        // If default is set and available, place it first
        $ordered = [];
        if ($default && isset($available[$default])) {
            $ordered[] = $available[$default];
            unset($available[$default]);
        }

        // Append remaining available providers in their mapping order
        foreach ($mapping as $key => $class) {
            if (isset($available[$key])) {
                $ordered[] = $available[$key];
            }
        }

        return $ordered;
    }
}
