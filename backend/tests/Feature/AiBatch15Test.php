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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AiBatch15Test extends TestCase
{
    use RefreshDatabase;

    protected $hrUser;

    protected $mentorUser;

    protected $company;

    protected $internship;

    protected $application;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'hr']);
        Role::firstOrCreate(['name' => 'mentor']);

        $this->company = Company::factory()->create();
        $this->hrUser = User::factory()->create();
        $this->hrUser->assignRole('hr');

        CompanyMember::create([
            'company_id' => $this->company->id,
            'user_id' => $this->hrUser->id,
            'role' => 'hr',
            'is_active' => true,
        ]);

        $this->mentorUser = User::factory()->create();
        $this->mentorUser->assignRole('mentor');

        // Add Mentor to company members
        CompanyMember::create([
            'company_id' => $this->company->id,
            'user_id' => $this->mentorUser->id,
            'role' => 'mentor',
            'is_active' => true,
        ]);

        $this->internship = Internship::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->application = Application::factory()->create([
            'internship_id' => $this->internship->id,
            'mentor_user_id' => $this->mentorUser->id,
            'status' => 'accepted',
        ]);

        // Create User Detail for Candidate
        UserDetail::create([
            'user_id' => $this->application->user_id,
            'skills' => ['PHP', 'Laravel'],
            'bio' => 'An aspiring developer.',
            'ai_consent' => true,
        ]);

        // Mock AI Service returning AiResponse object
        $this->mock(AiService::class, function ($mock) {
            $mock->shouldReceive('chat')->andReturn(new AiResponse('AI Response Text', ['provider' => 'fake']));
        });
    }

    public function test_hr_cannot_screen_candidate_from_other_company()
    {
        $otherCompany = Company::factory()->create();
        $otherInternship = Internship::factory()->create(['company_id' => $otherCompany->id]);
        $otherApplication = Application::factory()->create(['internship_id' => $otherInternship->id]);

        UserDetail::create([
            'user_id' => $otherApplication->user_id,
            'skills' => ['Java'],
            'bio' => 'Other bio.',
            'ai_consent' => true,
        ]);

        $this->actingAs($this->hrUser)
            ->withSession(['current_company_id' => $this->company->id])
            ->postJson(route('hr.ai.screen-candidate'), [
                'application_id' => $otherApplication->id,
            ])
            ->assertStatus(403);
    }

    public function test_hr_can_screen_candidate_from_own_company()
    {
        $this->actingAs($this->hrUser)
            ->withSession(['current_company_id' => $this->company->id])
            ->postJson(route('hr.ai.screen-candidate'), [
                'application_id' => $this->application->id,
            ])
            ->assertStatus(200)
            ->assertJsonPath('human_review_required', true);
    }

    public function test_mentor_cannot_generate_tasks_for_non_assigned_mentee()
    {
        $otherMentor = User::factory()->create();
        $otherMentor->assignRole('mentor');

        $otherApplication = Application::factory()->create([
            'internship_id' => $this->internship->id,
            'mentor_user_id' => $otherMentor->id,
        ]);

        $this->actingAs($this->mentorUser)
            ->withSession(['current_company_id' => $this->company->id])
            ->postJson(route('mentor.ai.generate-tasks'), [
                'application_id' => $otherApplication->id,
            ])
            ->assertStatus(403);
    }

    public function test_mentor_can_generate_tasks_for_assigned_mentee()
    {
        $this->actingAs($this->mentorUser)
            ->withSession(['current_company_id' => $this->company->id])
            ->postJson(route('mentor.ai.generate-tasks'), [
                'application_id' => $this->application->id,
            ])
            ->assertStatus(200)
            ->assertJsonPath('human_review_required', true);
    }

    public function test_hr_ai_insights_respect_company_scope()
    {
        $otherCompany = Company::factory()->create();
        $otherInternship = Internship::factory()->create(['company_id' => $otherCompany->id]);

        $this->actingAs($this->hrUser)
            ->withSession(['current_company_id' => $this->company->id])
            ->getJson(route('hr.ai.pipeline-insight', ['internship_id' => $otherInternship->id]))
            ->assertStatus(403);
    }
}
