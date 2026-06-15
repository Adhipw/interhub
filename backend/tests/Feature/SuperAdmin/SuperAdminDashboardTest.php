<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\ExternalIntegration;
use App\Models\FeatureFlag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuperAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        // Roles
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
    }

    public function test_super_admin_can_access_dashboard()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('super-admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_non_super_admin_is_denied()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('super-admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_role_change_creates_audit_and_security_event()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $superAdmin->assignRole('super_admin');

        $target = User::factory()->create(['role' => 'student']);
        $target->assignRole('student');

        $response = $this->actingAs($superAdmin)->patch(route('super-admin.users.update-role', $target->id), [
            'role' => 'admin',
        ]);

        $response->assertRedirect();

        $this->assertEquals('admin', $target->fresh()->role);
        $this->assertTrue($target->fresh()->hasRole('admin'));

        // Check Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'super_admin_role_change',
            'user_id' => $superAdmin->id,
        ]);

        // Check Security Event
        $this->assertDatabaseHas('security_events', [
            'event_type' => 'role_changed',
            'severity' => 'medium',
        ]);
    }

    public function test_feature_flag_toggle_creates_audit_log()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $superAdmin->assignRole('super_admin');

        $flag = FeatureFlag::create([
            'key' => 'test_feature',
            'name' => 'Test Feature',
            'is_enabled' => false,
        ]);

        $response = $this->actingAs($superAdmin)->post(route('super-admin.feature-flags.toggle', $flag->id));

        $response->assertRedirect();
        $this->assertTrue($flag->fresh()->is_enabled);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'super_admin_feature_toggle',
            'auditable_id' => $flag->id,
        ]);
    }

    public function test_sensitive_integration_credentials_are_masked_in_index()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $superAdmin->assignRole('super_admin');

        ExternalIntegration::create([
            'name' => 'Secret Service',
            'provider' => 'maganghub',
            'credentials' => ['api_key' => 'super-secret-key-123'],
        ]);

        $response = $this->actingAs($superAdmin)->get(route('super-admin.integrations.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('integrations.0.masked_credentials')
            ->where('integrations.0.masked_credentials.api_key', '********')
        );
    }
}
