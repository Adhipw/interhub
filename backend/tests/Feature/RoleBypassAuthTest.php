<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleBypassAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles
        Role::firstOrCreate(['name' => UserRole::ADMIN->value]);
        Role::firstOrCreate(['name' => UserRole::SUPER_ADMIN->value]);
        Role::firstOrCreate(['name' => UserRole::HR->value]);
        Role::firstOrCreate(['name' => UserRole::MENTOR->value]);
        Role::firstOrCreate(['name' => UserRole::USER->value]);
    }

    public function test_admin_can_access_dashboard_without_verification()
    {
        $admin = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $admin->assignRole(UserRole::ADMIN->value);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_super_admin_can_access_dashboard_without_verification()
    {
        $superAdmin = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $superAdmin->assignRole(UserRole::SUPER_ADMIN->value);

        $response = $this->actingAs($superAdmin)->get('/dashboard');

        $response->assertRedirect('/super-admin/dashboard');
    }

    public function test_hr_can_access_dashboard_without_verification()
    {
        $hr = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $hr->assignRole(UserRole::HR->value);

        $response = $this->actingAs($hr)->get('/dashboard');

        // HR is redirected to hr.dashboard by DashboardController, NOT to verify-email
        $response->assertRedirect(route('hr.dashboard'));
    }

    public function test_mentor_can_access_dashboard_without_verification()
    {
        $mentor = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $mentor->assignRole(UserRole::MENTOR->value);

        $response = $this->actingAs($mentor)->get('/dashboard');

        $response->assertRedirect('/mentor/dashboard');
    }

    public function test_regular_user_cannot_access_dashboard_without_verification()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $user->assignRole(UserRole::USER->value);

        $response = $this->actingAs($user)->get('/dashboard');

        // Should be redirected to verification notice
        $response->assertRedirect('/verify-email');
    }

    public function test_bypass_roles_are_redirected_from_verification_page_to_dashboard()
    {
        $admin = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $admin->assignRole(UserRole::ADMIN->value);

        $response = $this->actingAs($admin)->get('/verify-email');

        $response->assertRedirect('/dashboard');
    }
}
