<?php

namespace App\Services\AI;

use App\Services\AI\DTOs\AiResponse;
use App\Services\AI\Enums\AiRole;
use App\Services\AI\Logging\AiUsageLogger;
use App\Services\AI\Safety\SafetyGuard;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;

class AiService
{
    public function __construct(
        protected AiManager $manager,
        protected SafetyGuard $safety,
        protected AiUsageLogger $logger
    ) {}

    public function chat(array $messages, array $options = []): AiResponse
    {
        $skipAuth = $options['skip_auth'] ?? false;
        $rateLimitKey = $options['rate_limit_key'] ?? (Auth::id() ?: request()->ip());
        $maxRequests = $options['max_requests'] ?? config('ai.rate_limiting.max_requests_per_hour', 50);

        // 1. Authorization (if not skipped)
        if (! $skipAuth && Gate::denies('use-ai')) {
            throw new AuthorizationException('AI access denied.');
        }

        // 2. Rate Limiting
        $executed = RateLimiter::attempt(
            'ai-usage:'.$rateLimitKey,
            $maxRequests,
            function () {
                return true;
            },
            3600 // 1 hour
        );

        if (! $executed) {
            throw new \Exception('AI rate limit exceeded. Please try again later.');
        }

        // 3. Safety Check (Input - Only User messages)
        foreach ($messages as $msg) {
            if ($msg->role === AiRole::USER) {
                $this->safety->validateInput($msg->content);
            }
        }

        // 4. Generate
        $response = $this->manager->generate($messages, $options);

        // 5. Fairness & Safety Check (Output)
        $this->safety->validateFairness($response->content);
        $response->content = $this->safety->sanitizeOutput($response->content);

        // 6. Logging
        $this->logger->logUsage($messages, $response);

        return $response;
    }
}
