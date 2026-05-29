<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ChatbotProviders\ProviderInterface;
use App\Services\ChatbotProviders\GeminiProvider;
use App\Services\ChatbotProviders\OpenAiProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the ProviderInterface to the configured default provider (if any).
        $default = config('chatbot.default_provider');

        $map = [
            'gemini' => GeminiProvider::class,
            'openai' => OpenAiProvider::class,
        ];

        if ($default && isset($map[$default])) {
            $this->app->bind(ProviderInterface::class, $map[$default]);
            return;
        }

        // Otherwise, bind to the first provider that has an API key configured.
        foreach ($map as $key => $class) {
            if (config("services.{$key}.key")) {
                $this->app->bind(ProviderInterface::class, $class);
                return;
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
