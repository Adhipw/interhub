<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WalkthroughSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Student User
        $student = User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student Intern',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Create User Detail
        UserDetail::updateOrCreate(
            ['user_id' => $student->id],
            [
                'bio' => 'I am a passionate software engineering student looking for internship opportunities.',
                'phone_number' => '08123456789',
                'address' => 'Jakarta, Indonesia',
                'education' => [
                    ['school' => 'Universitas Indonesia', 'degree' => 'Bachelor of Computer Science', 'start_year' => 2021, 'end_year' => 2025],
                ],
                'skills' => ['Laravel', 'Vue.js', 'Tailwind CSS', 'TypeScript'],
                'cv_path' => null, // Will upload in walkthrough
            ]
        );

        // 3. Create Companies
        $gojek = Company::updateOrCreate(['slug' => 'gojek'], [
            'name' => 'Gojek',
            'location' => 'Jakarta',
            'website' => 'https://gojek.com',
            'description' => 'Southeast Asia\'s leading super-app.',
        ]);

        $traveloka = Company::updateOrCreate(['slug' => 'traveloka'], [
            'name' => 'Traveloka',
            'location' => 'Jakarta',
            'website' => 'https://traveloka.com',
            'description' => 'Southeast Asia\'s lifestyle super-app.',
        ]);

        // 4. Create Internships
        Internship::updateOrCreate(['slug' => 'software-engineer-intern-gojek'], [
            'company_id' => $gojek->id,
            'title' => 'Software Engineer Intern',
            'description' => 'Join our backend team to build scalable services.',
            'type' => 'Full-time',
            'location' => 'Jakarta / Remote',
            'status' => 'published',
            'is_paid' => true,
            'tags' => ['Backend', 'Go', 'Microservices'],
        ]);

        Internship::updateOrCreate(['slug' => 'frontend-engineer-intern-traveloka'], [
            'company_id' => $traveloka->id,
            'title' => 'Frontend Engineer Intern',
            'description' => 'Help us build beautiful and performant web interfaces.',
            'type' => 'Full-time',
            'location' => 'Jakarta',
            'status' => 'published',
            'is_paid' => true,
            'tags' => ['Frontend', 'React', 'TypeScript'],
        ]);

        Internship::updateOrCreate(['slug' => 'ui-ux-design-intern'], [
            'company_id' => $gojek->id,
            'title' => 'UI/UX Design Intern',
            'description' => 'Design the next generation of super-app features.',
            'type' => 'Full-time',
            'location' => 'Jakarta',
            'status' => 'published',
            'is_paid' => false,
            'tags' => ['Design', 'Figma', 'UX Research'],
        ]);
    }
}
