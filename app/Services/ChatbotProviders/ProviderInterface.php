<?php

namespace App\Services\ChatbotProviders;

use App\Models\User;

interface ProviderInterface
{
    /**
     * Send a message to provider and return structured reply array
     *
     * @param User|null $user
     * @param string $message
     * @param array $context
     * @return array ['text'=>string, 'model_name'=>string, 'prompt_tokens'=>int, 'completion_tokens'=>int, 'intent'=>string, 'suggestions'=>array]
     */
    public function reply(?User $user, string $message, array $context): array;
}
