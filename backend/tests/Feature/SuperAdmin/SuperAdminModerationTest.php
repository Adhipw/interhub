<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\FeatureFlag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_super_admin_can_access_dashboard(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_super_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('super-admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_super_admin_can_change_user_role_and_creates_events(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($superAdmin)
            ->patch(route('super-admin.users.update-role', $user), [
                'role' => 'admin',
            ]);

        $response->assertRedirect();
        $this->assertEquals('admin', $user->fresh()->role);

        // Verify Security Event
        $this->assertDatabaseHas('security_events', [
            'event_type' => 'role_changed',
            'user_id' => $superAdmin->id,
        ]);

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'super_admin_role_change',
            'user_id' => $superAdmin->id,
        ]);
    }

    public function test_super_admin_can_toggle_feature_flag_and_creates_log(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $flag = FeatureFlag::create([
            'name' => 'Test Feature',
            'key' => 'test_feature',
            'is_enabled' => false,
        ]);

        $response = $this->actingAs($superAdmin)
            ->post(route('super-admin.feature-flags.toggle', $flag));

        $response->assertRedirect();
        $this->assertTrue($flag->fresh()->is_enabled);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'super_admin_feature_toggle',
            'user_id' => $superAdmin->id,
        ]);
    }
}
