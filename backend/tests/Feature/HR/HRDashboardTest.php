<?php

namespace Tests\Feature\HR;

use App\Models\Application;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HRDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        // Create HR role for Spatie
        Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
    }

    public function test_hr_can_access_dashboard_with_company_context()
    {
        $hr = User::factory()->create(['role' => 'hr']);
        $hr->assignRole('hr');

        $company = Company::factory()->create();
        $hr->companyMemberships()->create([
            'company_id' => $company->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        $response = $this->actingAs($hr)
            ->withSession(['current_company_id' => $company->id])
            ->get(route('hr.dashboard'));

        $response->assertStatus(200);
    }

    public function test_hr_cannot_access_other_company()
    {
        $hr = User::factory()->create(['role' => 'hr']);
        $hr->assignRole('hr');

        $myCompany = Company::factory()->create();
        $otherCompany = Company::factory()->create();

        $hr->companyMemberships()->create([
            'company_id' => $myCompany->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        // Try to access other company dashboard via session manipulation
        $response = $this->actingAs($hr)
            ->withSession(['current_company_id' => $otherCompany->id])
            ->get(route('hr.dashboard'));

        // Should redirect back to selection because membership check fails in middleware
        $response->assertRedirect(route('hr.companies.select'));
    }

    public function test_hr_can_create_internship_for_own_company()
    {
        $hr = User::factory()->create(['role' => 'hr']);
        $hr->assignRole('hr');

        $company = Company::factory()->create();
        $hr->companyMemberships()->create([
            'company_id' => $company->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        $response = $this->actingAs($hr)
            ->withSession(['current_company_id' => $company->id])
            ->post(route('hr.internships.store'), [
                'title' => 'Backend Intern',
                'description' => 'A great position',
                'requirements' => 'PHP, Laravel',
                'benefits' => 'Salary',
                'type' => 'WFH',
                'location' => 'Jakarta',
                'deadline_at' => now()->addMonth()->format('Y-m-d'),
                'status' => 'published',
            ]);

        $response->assertRedirect(route('hr.internships.index'));
        $this->assertDatabaseHas('internships', [
            'company_id' => $company->id,
            'title' => 'Backend Intern',
        ]);
    }

    public function test_hr_can_review_application_in_scope()
    {
        $hr = User::factory()->create(['role' => 'hr']);
        $hr->assignRole('hr');

        $company = Company::factory()->create();
        $hr->companyMemberships()->create([
            'company_id' => $company->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        $student = User::factory()->create(['role' => 'user']);
        $internship = Internship::factory()->create(['company_id' => $company->id]);

        $application = Application::create([
            'user_id' => $student->id,
            'internship_id' => $internship->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($hr)
            ->withSession(['current_company_id' => $company->id])
            ->get(route('hr.applications.show', $application->id));

        $response->assertStatus(200);
    }

    public function test_hr_cannot_review_out_of_scope_application()
    {
        $hr = User::factory()->create(['role' => 'hr']);
        $hr->assignRole('hr');

        $myCompany = Company::factory()->create();
        $otherCompany = Company::factory()->create();

        $hr->companyMemberships()->create([
            'company_id' => $myCompany->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        $student = User::factory()->create(['role' => 'user']);
        $otherInternship = Internship::factory()->create(['company_id' => $otherCompany->id]);

        $application = Application::create([
            'user_id' => $student->id,
            'internship_id' => $otherInternship->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($hr)
            ->withSession(['current_company_id' => $myCompany->id])
            ->get(route('hr.applications.show', $application->id));

        $response->assertStatus(403);
    }
}
