<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $company;

    protected $application;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => UserRole::USER->value]);

        $this->user = User::factory()->create();
        $this->user->assignRole(UserRole::USER->value);

        $this->company = Company::factory()->create([
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'geofence_radius' => 100,
        ]);

        $internship = Internship::factory()->create([
            'company_id' => $this->company->id,
            'type' => 'Office',
        ]);

        $this->application = Application::factory()->create([
            'user_id' => $this->user->id,
            'internship_id' => $internship->id,
            'status' => 'accepted',
        ]);
    }

    public function test_check_in_requires_consent()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('attendance.check-in'), [
            'application_id' => $this->application->id,
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            // 'consent' missing
        ]);

        $response->assertSessionHasErrors('consent');
    }

    public function test_check_in_within_geofence()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('attendance.check-in'), [
            'application_id' => $this->application->id,
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'consent' => true,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'status' => 'present',
        ]);
    }

    public function test_check_in_outside_geofence_fails()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('attendance.check-in'), [
            'application_id' => $this->application->id,
            'latitude' => -7.200000, // Far away
            'longitude' => 110.816666,
            'consent' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('error', 'Anda berada di luar jangkauan lokasi perusahaan.');
    }
}
