<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Events\ProfileUpdated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoggingTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles manually as they might not be seeded in test environment
        Role::firstOrCreate(['name' => UserRole::SUPER_ADMIN->value]);
        Role::firstOrCreate(['name' => UserRole::USER->value]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole(UserRole::SUPER_ADMIN->value);

        $this->user = User::factory()->create();
        $this->user->assignRole(UserRole::USER->value);
    }

    public function test_activity_log_is_created_when_event_is_dispatched()
    {
        // For testing purposes, we manually call the listener or dispatch the event
        // We'll dispatch the event and check the database

        $oldData = ['name' => 'Old Name'];
        $newData = ['name' => 'New Name'];

        Event::dispatch(new ProfileUpdated($this->user, $oldData, $newData));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => null, // Event dispatching in test might not have Auth if not logged in
            'action' => 'profile_updated',
        ]);
    }

    public function test_audit_log_is_created_for_sensitive_events()
    {
        $oldData = ['name' => 'Old Name'];
        $newData = ['name' => 'New Name'];

        Event::dispatch(new ProfileUpdated($this->user, $oldData, $newData));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'profile_updated',
            'auditable_type' => User::class,
            'auditable_id' => $this->user->id,
        ]);
    }

    public function test_security_event_is_created_for_risk_events()
    {
        // Profile update is security risk if email changes
        $oldData = ['email' => 'old@example.com'];
        $newData = ['email' => 'new@example.com'];

        Event::dispatch(new ProfileUpdated($this->user, $oldData, $newData));

        $this->assertDatabaseHas('security_events', [
            'event_type' => 'profile_updated',
        ]);
    }

    public function test_unauthorized_user_cannot_access_logs()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('super-admin.activity-logs.index'));
        $response->assertStatus(403);

        $response = $this->get(route('super-admin.audit-logs.index'));
        $response->assertStatus(403);

        $response = $this->get(route('super-admin.security-events.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_logs()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('super-admin.activity-logs.index'));
        $response->assertStatus(200);

        $response = $this->get(route('super-admin.audit-logs.index'));
        $response->assertStatus(200);

        $response = $this->get(route('super-admin.security-events.index'));
        $response->assertStatus(200);
    }
}
