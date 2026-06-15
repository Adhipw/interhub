<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        foreach (PermissionEnum::cases() as $permission) {
            Permission::firstOrCreate(['name' => $permission->value]);
        }

        // Create roles and assign created permissions

        // SUPER ADMIN: All permissions handled by Gate::before
        Role::firstOrCreate(['name' => UserRole::SUPER_ADMIN->value]);

        // ADMIN: All permissions
        $adminRole = Role::firstOrCreate(['name' => UserRole::ADMIN->value]);
        $adminRole->syncPermissions(Permission::all());

        // HR: Company & Internship management
        $hrRole = Role::firstOrCreate(['name' => UserRole::HR->value]);
        $hrRole->syncPermissions([
            PermissionEnum::VIEW_COMPANIES->value,
            PermissionEnum::UPDATE_COMPANIES->value,
            PermissionEnum::VIEW_INTERNSHIPS->value,
            PermissionEnum::CREATE_INTERNSHIPS->value,
            PermissionEnum::UPDATE_INTERNSHIPS->value,
        ]);

        // MENTOR: View internships
        $mentorRole = Role::firstOrCreate(['name' => UserRole::MENTOR->value]);
        $mentorRole->syncPermissions([
            PermissionEnum::VIEW_INTERNSHIPS->value,
        ]);

        // USER: Basic view
        $userRole = Role::firstOrCreate(['name' => UserRole::USER->value]);
        $userRole->syncPermissions([
            PermissionEnum::VIEW_COMPANIES->value,
            PermissionEnum::VIEW_INTERNSHIPS->value,
        ]);
    }
}
