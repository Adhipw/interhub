<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use App\Services\AI\Safety\SafetyGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AiBatch16Test extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected $superAdminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'super_admin']);

        $this->adminUser = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $this->adminUser->assignRole('admin');

        $this->superAdminUser = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $this->superAdminUser->assignRole('super_admin');

        // Create dummy internship for moderation tests
        Company::factory()->create(['id' => 1]);
        Internship::factory()->create(['id' => 1, 'company_id' => 1]);

        config(['ai.default' => 'fake']);
        config(['ai.safety.blocked_keywords' => ['password', 'secret_key']]);
    }

    public function test_admin_can_access_moderation_ai()
    {
        $this->actingAs($this->adminUser)
            ->postJson(route('admin.ai.moderate-content'), [
                'type' => 'internship',
                'id' => 1, // ID doesn't matter for permission check
            ])
            ->assertStatus(200);
    }

    public function test_admin_cannot_access_super_admin_ai()
    {
        $this->actingAs($this->adminUser)
            ->getJson(route('super-admin.ai.security-risk-summary'))
            ->assertStatus(403);
    }

    public function test_super_admin_can_access_everything_ai()
    {
        $this->actingAs($this->superAdminUser)
            ->getJson(route('super-admin.ai.security-risk-summary'))
            ->assertStatus(200);

        $this->actingAs($this->superAdminUser)
            ->postJson(route('admin.ai.moderate-content'), [
                'type' => 'internship',
                'id' => 1,
            ])
            ->assertStatus(200);
    }

    public function test_ai_safety_violation_logs_security_event()
    {
        // Re-mock to test real logic or use the actual service if possible
        // Actually, let's test the SafetyGuard directly via an integration test

        $this->actingAs($this->superAdminUser)
            ->postJson(route('admin.ai.summarize-report'), [
                'report_content' => 'My secret password is 123456',
            ])
            ->assertStatus(422); // Because it aborts with 422 on blocked keyword

        $this->assertDatabaseHas('security_events', [
            'user_id' => $this->superAdminUser->id,
            'event_type' => 'AI_SAFETY_VIOLATION',
        ]);
    }

    public function test_secret_redaction_removes_api_keys()
    {
        $safety = new SafetyGuard;
        $text = "The key is api_key=abcd-1234-5678-efgh and secret: '1234567890abcdefghij'";

        $redacted = $safety->redactSecrets($text);

        $this->assertStringContainsString('[REDACTED]', $redacted);
        $this->assertStringNotContainsString('abcd-1234-5678-efgh', $redacted);
    }
}
