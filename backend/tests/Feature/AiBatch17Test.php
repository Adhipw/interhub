<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\Internship;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\AI\AiService;
use App\Services\AI\DTOs\AiResponse;
use App\Services\AI\Safety\SafetyGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AiBatch17Test extends TestCase
{
    use RefreshDatabase;

    protected $studentUser;

    protected $hrUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'hr']);

        $this->studentUser = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $this->studentUser->assignRole('student');

        $this->hrUser = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $this->hrUser->assignRole('hr');

        // Create company and membership for HR
        $company = Company::factory()->create(['id' => 1]);
        CompanyMember::create([
            'user_id' => $this->hrUser->id,
            'company_id' => $company->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        config(['ai.default' => 'fake']);

        Gate::define('use-ai', function ($user) {
            return true;
        });
    }

    public function test_user_without_consent_is_blocked()
    {
        UserDetail::factory()->create(['user_id' => $this->studentUser->id, 'ai_consent' => false]);

        $this->actingAs($this->studentUser)
            ->postJson(route('ai.review-profile'))
            ->assertStatus(403)
            ->assertJson(['error' => 'AI_CONSENT_REQUIRED']);
    }

    public function test_user_with_consent_can_access()
    {
        UserDetail::factory()->create(['user_id' => $this->studentUser->id, 'ai_consent' => true]);

        // Mock AiService
        $this->mock(AiService::class, function ($mock) {
            $mock->shouldReceive('chat')->andReturn(new AiResponse('AI Success', []));
        });

        $this->actingAs($this->studentUser)
            ->postJson(route('ai.review-profile'))
            ->assertStatus(200);
    }

    public function test_hr_cannot_summarize_candidate_without_candidate_consent()
    {
        $candidate = User::factory()->create();
        UserDetail::factory()->create(['user_id' => $candidate->id, 'ai_consent' => false]);

        $internship = Internship::factory()->create(['company_id' => 1]);
        $app = Application::factory()->create([
            'user_id' => $candidate->id,
            'internship_id' => $internship->id,
        ]);

        $this->actingAs($this->hrUser)
            ->withSession(['current_company_id' => 1])
            ->postJson(route('hr.ai.summarize-candidate'), ['application_id' => $app->id])
            ->assertStatus(403)
            ->assertJson(['error' => 'CANDIDATE_CONSENT_REQUIRED']);
    }

    public function test_pii_redaction_works()
    {
        $service = app(SafetyGuard::class);

        $input = 'Contact me at john@example.com or 081234567890';
        $redacted = $service->anonymizePII($input);

        $this->assertStringNotContainsString('john@example.com', $redacted);
        $this->assertStringNotContainsString('081234567890', $redacted);
        $this->assertStringContainsString('[EMAIL_REDACTED]', $redacted);
        $this->assertStringContainsString('[PHONE_REDACTED]', $redacted);
    }

    public function test_file_access_is_logged()
    {
        UserDetail::factory()->create([
            'user_id' => $this->studentUser->id,
            'ai_consent' => true,
            'cv_path' => 'cvs/test.pdf',
        ]);

        $this->mock(AiService::class, function ($mock) {
            $mock->shouldReceive('chat')->andReturn(new AiResponse('CV Summary', []));
        });

        $this->actingAs($this->studentUser)
            ->postJson(route('ai.summarize-cv'), [
                'cv_text' => 'Sample CV Content',
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('ai_file_access_logs', [
            'user_id' => $this->studentUser->id,
            'file_path' => 'cvs/test.pdf',
            'file_type' => 'cv',
        ]);
    }
}
