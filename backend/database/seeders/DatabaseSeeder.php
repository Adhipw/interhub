<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MasterDataSeeder::class,
            InternshipSeeder::class,
            RecruitmentPipelineSeeder::class,
            RolesAndPermissionsSeeder::class,
            SuperAdminSeeder::class,
            WalkthroughSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'role' => 'USER',
        ]);
    }
}
