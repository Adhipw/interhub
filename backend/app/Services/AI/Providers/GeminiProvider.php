<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTOs\AiResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AiProviderInterface
{
    protected string $apiKey;

    protected string $model;

    public function __construct(string $apiKey, string $model = 'gemini-1.5-flash')
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    public function generate(array $messages, array $options = []): AiResponse
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Gemini API Key is missing.');
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $systemInstruction = null;
        $contents = [];

        foreach ($messages as $msg) {
            if ($msg->role->value === 'system') {
                $systemInstruction = [
                    'parts' => [['text' => $msg->content]],
                ];
            } else {
                $contents[] = [
                    'role' => $msg->role->value === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg->content]],
                ];
            }
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? 0.7,
                'maxOutputTokens' => $options['max_tokens'] ?? 1000,
            ],
        ];

        if ($systemInstruction) {
            $payload['system_instruction'] = $systemInstruction;
        }

        $httpOptions = [];
        if (config('app.env') === 'local' && file_exists(storage_path('cacert.pem'))) {
            $httpOptions['verify'] = storage_path('cacert.pem');
        }

        $response = Http::withOptions($httpOptions)->post($url, $payload);

        if (! $response->successful()) {
            Log::error('Gemini API Error: '.$response->body());
            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? 'Failed to generate content from Gemini.';
            throw new \Exception($errorMessage);
        }

        $data = $response->json();

        if (! isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            Log::error('Gemini Unexpected Response: '.json_encode($data));
            throw new \Exception('Gemini returned an empty or unexpected response.');
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'];

        return new AiResponse($text, $data);
    }

    public function getIdentifier(): string
    {
        return 'gemini';
    }
}
