<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.toggle-status', $user));

        $response->assertRedirect();
        $this->assertFalse($user->fresh()->is_active);

        $this->assertTrue(AuditLog::where('action', 'admin_user_moderation')->exists());
    }

    public function test_admin_cannot_moderate_super_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.toggle-status', $superAdmin));

        $response->assertStatus(403);
        $this->assertTrue($superAdmin->fresh()->is_active);
    }

    public function test_admin_cannot_delete_super_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $superAdmin));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)
            ->get(route('admin.security.index'));

        $response->assertStatus(403);
    }
}
