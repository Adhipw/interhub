<?php

use App\Models\User;
use App\Notifications\Auth\EmailVerificationOtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('user can register with valid data', function () {
    Notification::fake();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone_number' => '08123456789',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'captcha' => 'passed',
    ]);

    $response->assertRedirect('/verify-email');
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);

    Notification::assertSentTo(
        User::where('email', 'test@example.com')->first(),
        EmailVerificationOtpNotification::class
    );
});

test('user can login with correct credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password123!'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'Password123!',
        'captcha' => 'passed',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('login attempt is logged', function () {
    $email = 'wrong@example.com';

    $this->post('/login', [
        'email' => $email,
        'password' => 'wrong-password',
        'captcha' => 'passed',
    ]);

    $this->assertDatabaseHas('login_attempts', [
        'email' => $email,
        'is_successful' => false,
    ]);
});
