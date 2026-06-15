<?php

namespace Tests\Feature\Mentor;

use App\Models\Application;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MentorDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        // Create the mentor role for Spatie
        Role::firstOrCreate(['name' => 'mentor', 'guard_name' => 'web']);
    }

    public function test_mentor_can_access_dashboard()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $mentor->assignRole('mentor');

        $company = Company::factory()->create();
        $mentor->companyMemberships()->create([
            'company_id' => $company->id,
            'role' => 'mentor',
            'is_active' => true,
        ]);

        $response = $this->actingAs($mentor)
            ->withSession(['current_company_id' => $company->id])
            ->get(route('mentor.dashboard'));

        $response->assertStatus(200);
    }

    public function test_mentor_can_view_assigned_mentee()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $mentor->assignRole('mentor');

        $company = Company::factory()->create();
        $mentor->companyMemberships()->create([
            'company_id' => $company->id,
            'role' => 'mentor',
            'is_active' => true,
        ]);

        $student = User::factory()->create(['role' => 'user']);
        $internship = Internship::factory()->create(['company_id' => $company->id]);

        $application = new Application;
        $application->forceFill([
            'user_id' => $student->id,
            'internship_id' => $internship->id,
            'status' => 'accepted',
            'mentor_user_id' => $mentor->id,
        ])->save();

        $response = $this->actingAs($mentor)
            ->withSession(['current_company_id' => $company->id])
            ->get(route('mentor.mentees.show', $application->id));

        $response->assertStatus(200);
    }

    public function test_mentor_cannot_view_unassigned_mentee()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $mentor->assignRole('mentor');

        $company = Company::factory()->create();
        $mentor->companyMemberships()->create([
            'company_id' => $company->id,
            'role' => 'mentor',
            'is_active' => true,
        ]);

        $otherMentor = User::factory()->create(['role' => 'mentor']);
        $student = User::factory()->create(['role' => 'user']);
        $internship = Internship::factory()->create(['company_id' => $company->id]);

        $application = new Application;
        $application->forceFill([
            'user_id' => $student->id,
            'internship_id' => $internship->id,
            'status' => 'accepted',
            'mentor_user_id' => $otherMentor->id,
        ])->save();

        $response = $this->actingAs($mentor)
            ->withSession(['current_company_id' => $company->id])
            ->get(route('mentor.mentees.show', $application->id));

        $response->assertStatus(403);
    }

    public function test_mentor_can_create_task_for_assigned_mentee()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $mentor->assignRole('mentor');

        $company = Company::factory()->create();
        $mentor->companyMemberships()->create([
            'company_id' => $company->id,
            'role' => 'mentor',
            'is_active' => true,
        ]);

        $student = User::factory()->create(['role' => 'user']);
        $internship = Internship::factory()->create(['company_id' => $company->id]);

        $application = new Application;
        $application->forceFill([
            'user_id' => $student->id,
            'internship_id' => $internship->id,
            'status' => 'accepted',
            'mentor_user_id' => $mentor->id,
        ])->save();

        $response = $this->actingAs($mentor)
            ->withSession(['current_company_id' => $company->id])
            ->post(route('mentor.tasks.store', $application->id), [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'priority' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('mentor_tasks', [
            'application_id' => $application->id,
            'title' => 'Test Task',
        ]);
    }

    public function test_mentor_can_submit_feedback()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $mentor->assignRole('mentor');

        $company = Company::factory()->create();
        $mentor->companyMemberships()->create([
            'company_id' => $company->id,
            'role' => 'mentor',
            'is_active' => true,
        ]);

        $student = User::factory()->create(['role' => 'user']);
        $internship = Internship::factory()->create(['company_id' => $company->id]);

        $application = new Application;
        $application->forceFill([
            'user_id' => $student->id,
            'internship_id' => $internship->id,
            'status' => 'accepted',
            'mentor_user_id' => $mentor->id,
        ])->save();

        $response = $this->actingAs($mentor)
            ->withSession(['current_company_id' => $company->id])
            ->post(route('mentor.mentees.feedback', $application->id), [
                'content' => 'Great work on the first week!',
                'assessment' => [
                    'technical' => 5,
                    'soft_skills' => 4,
                    'attitude' => 5,
                ],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('mentor_feedback', [
            'application_id' => $application->id,
            'content' => 'Great work on the first week!',
        ]);
    }
}
