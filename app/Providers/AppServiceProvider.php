<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ChatbotProviders\ProviderInterface;
use App\Services\ChatbotProviders\GeminiProvider;

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
        ];

        if ($default && isset($map[$default])) {
            $this->app->bind(ProviderInterface::class, $map[$default]);
            return;
        }

        // Bind Gemini when it has a key configured.
        if (config('services.gemini.key')) {
            $this->app->bind(ProviderInterface::class, GeminiProvider::class);
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
