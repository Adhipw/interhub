<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AI\AiManager;
use App\Services\AI\AiService;
use App\Services\AI\DTOs\AiMessage;
use App\Services\AI\DTOs\AiResponse;
use App\Services\AI\Enums\AiRole;
use App\Services\AI\Logging\AiUsageLogger;
use App\Services\AI\Safety\SafetyGuard;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected AiService $aiService;

    protected function setUp(): void
    {
        parent::setUp();

        $manager = new AiManager($this->app);
        $safety = new SafetyGuard;
        $logger = new AiUsageLogger;

        $this->aiService = new AiService($manager, $safety, $logger);
    }

    public function test_fake_ai_provider_works()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $this->actingAs($user);

        $messages = [new AiMessage(AiRole::USER, 'Hello AI')];
        $response = $this->aiService->chat($messages);

        $this->assertStringContainsString('fake AI response', $response->content);
        $this->assertDatabaseHas('ai_usage_logs', ['user_id' => $user->id]);
    }

    public function test_ai_authorization_gate()
    {
        $user = User::factory()->create(['email_verified_at' => null]); // Unverified
        $this->actingAs($user);

        $this->expectException(AuthorizationException::class);

        $messages = [new AiMessage(AiRole::USER, 'Hello AI')];
        $this->aiService->chat($messages);
    }

    public function test_ai_rate_limit()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $this->actingAs($user);

        config(['ai.rate_limiting.max_requests_per_hour' => 1]);

        $messages = [new AiMessage(AiRole::USER, 'Request 1')];
        $this->aiService->chat($messages); // Should pass

        $this->expectExceptionMessage('AI rate limit exceeded');
        $this->aiService->chat($messages); // Should fail
    }

    public function test_ai_safety_guard_input()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $this->actingAs($user);

        $this->expectExceptionMessage('Input contains blocked sensitive keyword');

        $messages = [new AiMessage(AiRole::USER, 'Tell me my password')];
        $this->aiService->chat($messages);
    }

    public function test_ai_safety_guard_output_redaction()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $this->actingAs($user);

        // We mock the manager to return a sensitive string
        $mockManager = \Mockery::mock(AiManager::class);
        $mockManager->shouldReceive('generate')->andReturn(
            new AiResponse('Your secret is safe', ['provider' => 'mock'])
        );

        $service = new AiService($mockManager, new SafetyGuard, new AiUsageLogger);

        $messages = [new AiMessage(AiRole::USER, 'Hello')];
        $response = $service->chat($messages);

        $this->assertStringContainsString('[REDACTED]', $response->content);
    }
}
