<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultAdminAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Default Admin',
                'email' => 'admin1@internhub.id',
                'password' => 'admin123',
                'role' => UserRole::ADMIN->value,
            ],
            [
                'name' => 'Default Super Admin',
                'email' => 'superadmin1@internhub.id',
                'password' => 'superadmin123',
                'role' => UserRole::SUPER_ADMIN->value,
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                    'role' => $account['role'],
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            $user->syncRoles([$account['role']]);
        }
    }
}
