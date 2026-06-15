<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_dispatched()
    {
        $this->withoutVite();
        Notification::fake();

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $internship = Internship::factory()->create(['company_id' => $company->id]);
        $application = Application::factory()->create([
            'user_id' => $user->id,
            'internship_id' => $internship->id,
            'status' => 'pending',
        ]);

        $user->notify(new ApplicationStatusUpdated($application));

        Notification::assertSentTo($user, ApplicationStatusUpdated::class);
    }

    public function test_horizon_access_restriction()
    {
        $this->withoutVite();
        $user = User::factory()->create(['role' => 'student']);
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($user)->get('/horizon')->assertStatus(403);
        $this->actingAs($admin)->get('/horizon')->assertStatus(200);
    }
}
