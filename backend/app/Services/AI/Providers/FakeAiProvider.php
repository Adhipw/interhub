<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTOs\AiResponse;

class FakeAiProvider implements AiProviderInterface
{
    public function generate(array $messages, array $options = []): AiResponse
    {
        return new AiResponse(
            'This is a fake AI response for testing purposes.',
            ['model' => 'fake-model'],
            10
        );
    }

    public function getIdentifier(): string
    {
        return 'fake';
    }
}
