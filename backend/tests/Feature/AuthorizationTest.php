<?php

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('super admin can bypass all gates', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SUPER_ADMIN->value);

    $this->actingAs($superAdmin);

    expect(Gate::allows(Permission::DELETE_COMPANIES->value))->toBeTrue();
});

test('user cannot access admin-only routes', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::USER->value);

    $this->actingAs($user);

    // Mock an admin route protected by middleware
    Route::get('/auth/test-admin', function () {
        return 'success';
    })->middleware(['auth', 'role:admin']);

    $response = $this->get('/auth/test-admin');
    $response->assertStatus(403);
});

test('hr can manage internships', function () {
    $hr = User::factory()->create();
    $hr->assignRole(UserRole::HR->value);

    $this->actingAs($hr);

    expect(Gate::allows(Permission::CREATE_INTERNSHIPS->value))->toBeTrue();
    expect(Gate::allows(Permission::DELETE_COMPANIES->value))->toBeFalse();
});
