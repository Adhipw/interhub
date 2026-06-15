<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\ExternalIntegration;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        // Roles
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    public function test_admin_can_toggle_user_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $target = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.users.toggle-status', $target->id));

        $response->assertRedirect();
        $this->assertFalse($target->fresh()->is_active);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_user_moderation',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_cannot_moderate_super_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($admin)->post(route('admin.users.toggle-status', $superAdmin->id));

        $response->assertStatus(403);
        $this->assertTrue($superAdmin->fresh()->is_active);
    }

    public function test_admin_can_verify_company()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $company = Company::factory()->create(['is_verified' => false]);

        $response = $this->actingAs($admin)->post(route('admin.companies.verify', $company->id));

        $response->assertRedirect();
        $this->assertTrue($company->fresh()->is_verified);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_company_verified',
            'auditable_id' => $company->id,
        ]);
    }

    public function test_admin_cannot_access_super_admin_exclusive_routes()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('super-admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_secret_integration()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $secretIntegration = ExternalIntegration::create([
            'name' => 'Secret API',
            'provider' => 'maganghub',
            'is_secret' => true,
        ]);

        // Index should not show it
        $response = $this->actingAs($admin)->get(route('admin.integrations.index'));
        $response->assertJsonMissing(['name' => 'Secret API']);

        // Show should be forbidden
        $response = $this->actingAs($admin)->get(route('admin.integrations.show', $secretIntegration->id));
        $response->assertStatus(403);
    }

    public function test_admin_only_sees_low_and_medium_security_events()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        SecurityEvent::create([
            'user_id' => $admin->id,
            'event_type' => 'LOW_EVENT',
            'severity' => 'low',
            'description' => 'Normal event',
        ]);

        SecurityEvent::create([
            'user_id' => $admin->id,
            'event_type' => 'CRITICAL_EVENT',
            'severity' => 'critical',
            'description' => 'Sensitive event',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.security.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('events.data', 1)
            ->where('events.data.0.event_type', 'LOW_EVENT')
        );
    }
}
