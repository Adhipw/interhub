<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InternshipSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create HR Users
        $hr1 = User::create([
            'name' => 'HR Manager TechNova',
            'email' => 'hr@technova.id',
            'password' => bcrypt('password'),
            'role' => 'HR',
            'email_verified_at' => now(),
        ]);

        $hr2 = User::create([
            'name' => 'Recruiter Creative Digital',
            'email' => 'hr@creativedigital.id',
            'password' => bcrypt('password'),
            'role' => 'HR',
            'email_verified_at' => now(),
        ]);

        // 2. Create Companies
        $company1 = Company::create([
            'name' => 'TechNova Solutions',
            'slug' => 'technova-solutions',
            'description' => 'Perusahaan teknologi terkemuka yang fokus pada solusi AI dan Big Data.',
            'location' => 'Jakarta Selatan, Indonesia',
            'website' => 'https://technova.id',
            'logo_url' => 'https://api.dicebear.com/7.x/initials/svg?seed=TN',
        ]);

        $company2 = Company::create([
            'name' => 'Creative Digital Agency',
            'slug' => 'creative-digital',
            'description' => 'Agency kreatif yang membantu brand bertransformasi di era digital.',
            'location' => 'Bandung, Jawa Barat',
            'website' => 'https://creativedigital.id',
            'logo_url' => 'https://api.dicebear.com/7.x/initials/svg?seed=CD',
        ]);

        // 3. Create Internships
        Internship::create([
            'company_id' => $company1->id,
            'title' => 'UI/UX Design Intern',
            'slug' => 'ui-ux-design-intern-'.Str::random(5),
            'description' => 'Membantu tim desain dalam membuat antarmuka aplikasi mobile.',
            'requirements' => ['Figma', 'Adobe XD', 'Passionate about design'],
            'location' => 'Jakarta (Remote)',
            'type' => 'Full-time',
            'stipend' => 'Rp 2.000.000 - 3.500.000',
            'deadline_at' => now()->addDays(30),
            'status' => 'published',
            'is_paid' => true,
            'tags' => ['Figma', 'UI Design', 'UX Research'],
        ]);

        Internship::create([
            'company_id' => $company1->id,
            'title' => 'Backend Developer (Laravel)',
            'slug' => 'backend-developer-laravel-'.Str::random(5),
            'description' => 'Membangun API robust menggunakan Laravel 11.',
            'requirements' => ['PHP', 'Laravel', 'MySQL', 'Git'],
            'location' => 'Jakarta (Hybrid)',
            'type' => 'Full-time',
            'stipend' => 'Rp 3.000.000 - 5.000.000',
            'deadline_at' => now()->addDays(45),
            'status' => 'published',
            'is_paid' => true,
            'tags' => ['PHP', 'Laravel', 'MySQL'],
        ]);

        Internship::create([
            'company_id' => $company2->id,
            'title' => 'Social Media Specialist',
            'slug' => 'social-media-specialist-'.Str::random(5),
            'description' => 'Mengelola konten media sosial untuk klien agency.',
            'requirements' => ['Copywriting', 'Content Planning', 'TikTok/Instagram savvy'],
            'location' => 'Bandung',
            'type' => 'On-site',
            'stipend' => 'Insentif Berbasis Kinerja',
            'deadline_at' => now()->addDays(20),
            'status' => 'published',
            'is_paid' => false,
            'tags' => ['Copywriting', 'Instagram', 'Tiktok'],
        ]);

        Internship::create([
            'company_id' => $company2->id,
            'title' => 'Frontend Engineer (Vue.js)',
            'slug' => 'frontend-engineer-vuejs-'.Str::random(5),
            'description' => 'Mengembangkan dashboard internal menggunakan Vue 3 dan Tailwind CSS.',
            'requirements' => ['JavaScript', 'Vue.js', 'CSS', 'Tailwind'],
            'location' => 'Remote',
            'type' => 'Part-time',
            'stipend' => 'Rp 2.500.000 - 4.000.000',
            'deadline_at' => now()->addDays(60),
            'status' => 'published',
            'is_paid' => true,
            'tags' => ['Vue.js', 'Tailwind', 'JavaScript'],
        ]);
    }
}
