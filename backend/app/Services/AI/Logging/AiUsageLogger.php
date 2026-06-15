<?php

namespace App\Services\AI\Logging;

use App\Services\AI\DTOs\AiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AiUsageLogger
{
    public function logUsage(array $messages, AiResponse $response): void
    {
        DB::table('ai_usage_logs')->insert([
            'user_id' => Auth::id(),
            'provider' => $response->metadata['provider'] ?? 'unknown',
            'model' => $response->metadata['model'] ?? null,
            'prompt_summary' => json_encode($this->summarizePrompt($messages)),
            'response_content' => $response->content,
            'tokens_used' => $response->tokensUsed,
            'metadata' => json_encode($response->metadata),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function summarizePrompt(array $messages): array
    {
        return array_map(fn ($m) => [
            'role' => $m->role->value,
            'length' => strlen($m->content),
        ], $messages);
    }
}
