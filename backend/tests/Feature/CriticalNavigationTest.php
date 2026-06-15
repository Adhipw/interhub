<?php

use App\Models\Application;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

function navigationTestUser(array $overrides = []): User
{
    $user = User::factory()->create(array_merge([
        'password' => Hash::make('Password123!'),
    ], $overrides));

    UserDetail::create([
        'user_id' => $user->id,
        'bio' => 'Test bio',
        'address' => 'Jakarta',
        'skills' => ['Laravel', 'Vue'],
        'education' => [
            ['school' => 'University', 'degree' => 'S1', 'start_year' => 2020, 'end_year' => 2024],
        ],
        'cv_path' => 'private/cvs/test-cv.pdf',
    ]);

    return $user;
}

function navigationTestInternship(?Company $company = null): Internship
{
    $company ??= Company::factory()->create();

    return Internship::factory()->create([
        'company_id' => $company->id,
        'status' => 'published',
    ]);
}

test('homepage loads as the public landing page', function () {
    $response = $this->get(route('welcome'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Welcome', false));
});

test('user can login and open the dashboard directly from the URL', function () {
    $user = navigationTestUser();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'Password123!',
        'captcha' => 'passed',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Dashboard', false));
});

test('user can apply to an internship and see it in my applications', function () {
    $user = navigationTestUser();
    $internship = navigationTestInternship();

    $this->actingAs($user)
        ->post(route('internships.apply', $internship->slug), [
            'cover_letter' => 'Saya tertarik melamar posisi ini.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('applications', [
        'user_id' => $user->id,
        'internship_id' => $internship->id,
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->get(route('applications.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Applications/Index', false)
            ->where('applications.data', fn ($applications) => count($applications) === 1)
        );

    $this->actingAs($user)
        ->get('/applications')
        ->assertRedirect('/my-applications');
});

test('hr can open dashboard and see applicant list', function () {
    $hr = navigationTestUser(['role' => 'hr']);
    $hr->assignRole('hr');

    $company = Company::factory()->create();
    $hr->companyMemberships()->create([
        'company_id' => $company->id,
        'role' => 'owner',
        'is_active' => true,
    ]);

    $internship = navigationTestInternship($company);
    $student = navigationTestUser();

    Application::create([
        'user_id' => $student->id,
        'internship_id' => $internship->id,
        'status' => 'pending',
        'cover_letter' => 'Saya ingin belajar di sini.',
        'cv_snapshot' => 'private/cvs/test-cv.pdf',
    ]);

    $this->actingAs($hr)
        ->withSession(['current_company_id' => $company->id])
        ->get(route('hr.dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('HR/Dashboard', false));

    $this->actingAs($hr)
        ->withSession(['current_company_id' => $company->id])
        ->get(route('hr.applications.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('HR/Applications/Index', false)
            ->where('applications.data', fn ($applications) => count($applications) === 1)
        );
});

test('reset password and verify email pages render without error', function () {
    $unverifiedUser = navigationTestUser(['email_verified_at' => null]);

    $this->get(route('password.reset', ['email' => $unverifiedUser->email]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Auth/ResetPassword', false));

    $this->actingAs($unverifiedUser)
        ->get(route('verification.notice'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Auth/VerifyEmail', false));
});
