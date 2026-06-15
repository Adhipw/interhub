<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTOs\AiMessage;
use App\Services\AI\DTOs\AiResponse;

interface AiProviderInterface
{
    /**
     * Generate a response from the AI model.
     *
     * @param  AiMessage[]  $messages
     */
    public function generate(array $messages, array $options = []): AiResponse;

    /**
     * Get the provider identifier.
     */
    public function getIdentifier(): string;
}
