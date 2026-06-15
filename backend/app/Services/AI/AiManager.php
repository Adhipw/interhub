<?php

namespace App\Services\AI;

use App\Services\AI\Providers\AiProviderInterface;
use App\Services\AI\Providers\FakeAiProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\LocalLlmProvider;
use Illuminate\Support\Manager;

class AiManager extends Manager
{
    public function getDefaultDriver()
    {
        return config('ai.default', 'fake');
    }

    public function createGeminiDriver(): AiProviderInterface
    {
        return new GeminiProvider(
            config('ai.providers.gemini.key'),
            config('ai.providers.gemini.model', 'gemini-1.5-flash')
        );
    }

    public function createLocalDriver(): AiProviderInterface
    {
        return new LocalLlmProvider(
            config('ai.providers.local.base_url', 'http://localhost:11434'),
            config('ai.providers.local.model', 'llama3')
        );
    }

    public function createFakeDriver(): AiProviderInterface
    {
        return new FakeAiProvider;
    }
}
