<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTOs\AiResponse;
use Illuminate\Support\Facades\Http;

class LocalLlmProvider implements AiProviderInterface
{
    protected string $baseUrl;

    protected string $model;

    public function __construct(string $baseUrl, string $model = 'llama3')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->model = $model;
    }

    public function generate(array $messages, array $options = []): AiResponse
    {
        $response = Http::post("{$this->baseUrl}/v1/chat/completions", [
            'model' => $this->model,
            'messages' => array_map(fn ($m) => $m->toArray(), $messages),
            'temperature' => $options['temperature'] ?? 0.7,
        ]);

        if (! $response->successful()) {
            throw new \Exception('Local LLM API Error: '.$response->status());
        }

        $data = $response->json();
        $text = $data['choices'][0]['message']['content'] ?? '';

        return new AiResponse($text, $data);
    }

    public function getIdentifier(): string
    {
        return 'local';
    }
}
