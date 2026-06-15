<?php

use App\Models\Application;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// ═══════════════════════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════════════════════

/**
 * Create a verified user with a complete profile (including CV path).
 */
function userWithProfile(array $overrides = []): User
{
    $user = User::factory()->create($overrides);
    UserDetail::create([
        'user_id' => $user->id,
        'bio' => 'Test bio',
        'address' => 'Test address',
        'skills' => ['Laravel', 'Vue.js'],
        'education' => [
            ['school' => 'UI', 'degree' => 'S1 Informatika', 'start_year' => 2020, 'end_year' => 2024],
        ],
        'cv_path' => 'private/cvs/cv_test_file.pdf',
    ]);

    return $user->fresh(['detail']);
}

/**
 * Create a published internship with a company.
 */
function publishedInternship(): Internship
{
    $company = Company::create([
        'name' => 'Test Company',
        'slug' => 'test-company-'.uniqid(),
        'location' => 'Jakarta',
    ]);

    return Internship::create([
        'company_id' => $company->id,
        'title' => 'Software Engineer Intern',
        'slug' => 'software-engineer-intern-'.uniqid(),
        'description' => 'Test description',
        'type' => 'WFH',
        'status' => 'published',
    ]);
}

// ═══════════════════════════════════════════════════════════════
// PROFILE TESTS
// ═══════════════════════════════════════════════════════════════

test('authenticated user can update their own profile', function () {
    $user = userWithProfile();

    $response = $this->actingAs($user)->post('/profile', [
        'bio' => 'My new bio',
        'phone_number' => '08123456789',
        'address' => 'Jl. Test No. 1',
        'education' => [
            ['school' => 'ITB', 'degree' => 'S1 Teknik', 'start_year' => 2020, 'end_year' => 2024],
        ],
        'skills' => ['PHP', 'Vue'],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status', 'profile-updated');

    $this->assertDatabaseHas('user_details', [
        'user_id' => $user->id,
        'bio' => 'My new bio',
        'address' => 'Jl. Test No. 1',
    ]);
});

test('user cannot update another users profile', function () {
    $owner = userWithProfile();
    $hacker = User::factory()->create();

    // Hacker posts to profile route — this always updates the authenticated user's own profile
    // The route is locked to Auth::user(), so there's no way to pass a user_id target.
    // We verify the owner's profile is UNCHANGED after hacker's update.
    $this->actingAs($hacker)->post('/profile', [
        'bio' => 'Hacked bio',
    ]);

    $this->assertDatabaseMissing('user_details', [
        'user_id' => $owner->id,
        'bio' => 'Hacked bio',
    ]);
});

test('unauthenticated user cannot access profile page', function () {
    $response = $this->get('/profile');
    $response->assertRedirect('/login');
});

test('unverified user cannot access profile page', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/profile');
    $response->assertRedirect('/verify-email');
});

// ═══════════════════════════════════════════════════════════════
// APPLICATION TESTS
// ═══════════════════════════════════════════════════════════════

test('user with cv can apply to a published internship', function () {
    $user = userWithProfile();
    $internship = publishedInternship();

    $response = $this->actingAs($user)->post("/internships/{$internship->slug}/apply", [
        'cover_letter' => 'Saya sangat tertarik dengan posisi ini.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status', 'application-submitted');

    $this->assertDatabaseHas('applications', [
        'user_id' => $user->id,
        'internship_id' => $internship->id,
        'status' => 'pending',
        'cv_snapshot' => 'private/cvs/cv_test_file.pdf',
    ]);
});

test('user without cv cannot apply to an internship', function () {
    $user = User::factory()->create(); // no UserDetail / cv_path
    $internship = publishedInternship();

    $response = $this->actingAs($user)->post("/internships/{$internship->slug}/apply", [
        'cover_letter' => 'Test',
    ]);

    $response->assertSessionHasErrors('application');
    $this->assertDatabaseMissing('applications', [
        'user_id' => $user->id,
        'internship_id' => $internship->id,
    ]);
});

test('user cannot apply to the same internship twice', function () {
    $user = userWithProfile();
    $internship = publishedInternship();

    // First application
    $this->actingAs($user)->post("/internships/{$internship->slug}/apply");

    // Second attempt
    $response = $this->actingAs($user)->post("/internships/{$internship->slug}/apply");

    $response->assertSessionHasErrors('application');
    $this->assertDatabaseCount('applications', 1);
});

test('application initial status is pending', function () {
    $user = userWithProfile();
    $internship = publishedInternship();

    $this->actingAs($user)->post("/internships/{$internship->slug}/apply");

    $application = Application::where('user_id', $user->id)->first();
    expect($application->status)->toBe('pending');
});

test('application timeline is initialized on creation', function () {
    $user = userWithProfile();
    $internship = publishedInternship();

    $this->actingAs($user)->post("/internships/{$internship->slug}/apply");

    $application = Application::where('user_id', $user->id)->first();
    expect($application->timeline)->toBeArray();
    expect($application->timeline[0]['status'])->toBe('pending');
    expect($application->timeline[0]['label'])->toBe('Lamaran Dikirim');
});

test('api user with cv can apply and see application in api index', function () {
    $user = userWithProfile();
    $internship = publishedInternship();
    Sanctum::actingAs($user);

    $this->postJson("/api/v1/internships/{$internship->slug}/apply", [
        'cover_letter' => 'Saya sangat tertarik dengan posisi ini.',
    ])
        ->assertCreated()
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.internship_id', $internship->id);

    $this->getJson('/api/v1/applications')
        ->assertOk()
        ->assertJsonPath('data.data.0.user_id', $user->id)
        ->assertJsonPath('data.data.0.internship_id', $internship->id)
        ->assertJsonPath('data.total', 1);
});

// ═══════════════════════════════════════════════════════════════
// APPLICATION TRACKING — DATA ISOLATION
// ═══════════════════════════════════════════════════════════════

test('user can only view their own applications on index', function () {
    $user1 = userWithProfile();
    $user2 = userWithProfile();
    $internship = publishedInternship();

    // User2 applies
    Application::create([
        'user_id' => $user2->id,
        'internship_id' => $internship->id,
        'status' => 'pending',
    ]);

    // User1 visits /my-applications — should NOT see user2's application
    $response = $this->actingAs($user1)->get('/my-applications');
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Applications/Index', false)
        ->where('applications.data', fn ($apps) => count($apps) === 0)
    );
});

test('user cannot view another users application detail', function () {
    $owner = userWithProfile();
    $hacker = userWithProfile();
    $internship = publishedInternship();

    $application = Application::create([
        'user_id' => $owner->id,
        'internship_id' => $internship->id,
        'status' => 'pending',
    ]);

    // Hacker tries to access owner's application
    $response = $this->actingAs($hacker)->get("/my-applications/{$application->id}");
    $response->assertStatus(403);
});

// ═══════════════════════════════════════════════════════════════
// FILE ACCESS POLICY
// ═══════════════════════════════════════════════════════════════

test('owner can access their own private file', function () {
    Storage::fake();
    Storage::put('private/cvs/cv_owner_test.pdf', 'PDF content');

    $user = User::factory()->create();
    UserDetail::create([
        'user_id' => $user->id,
        'cv_path' => 'private/cvs/cv_owner_test.pdf',
    ]);

    $url = URL::temporarySignedRoute(
        'storage.private',
        now()->addMinutes(5),
        ['type' => 'cvs', 'filename' => 'cv_owner_test.pdf']
    );

    $response = $this->actingAs($user)->get($url);
    $response->assertOk();
});

test('another user cannot access someone elses private file', function () {
    Storage::fake();
    Storage::put('private/cvs/cv_owner_secret.pdf', 'PDF content');

    $owner = User::factory()->create();
    UserDetail::create([
        'user_id' => $owner->id,
        'cv_path' => 'private/cvs/cv_owner_secret.pdf',
    ]);

    $hacker = User::factory()->create();
    UserDetail::create([
        'user_id' => $hacker->id,
        'cv_path' => null, // hacker has no CV
    ]);

    $url = URL::temporarySignedRoute(
        'storage.private',
        now()->addMinutes(5),
        ['type' => 'cvs', 'filename' => 'cv_owner_secret.pdf']
    );

    $response = $this->actingAs($hacker)->get($url);
    $response->assertStatus(403);
});

test('unauthenticated user cannot access any private file', function () {
    Storage::fake();
    Storage::put('private/cvs/cv_public_attempt.pdf', 'PDF content');

    $url = URL::temporarySignedRoute(
        'storage.private',
        now()->addMinutes(5),
        ['type' => 'cvs', 'filename' => 'cv_public_attempt.pdf']
    );

    $response = $this->get($url);
    $response->assertRedirect('/login');
});

test('accessing a nonexistent private file returns 404', function () {
    $user = userWithProfile();

    $url = URL::temporarySignedRoute(
        'storage.private',
        now()->addMinutes(5),
        ['type' => 'cvs', 'filename' => 'nonexistent_file.pdf']
    );

    $response = $this->actingAs($user)->get($url);
    $response->assertStatus(404);
});
