<?php

namespace Database\Seeders;

use App\Enums\CompanyRole;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Company;
use App\Models\Internship;
use App\Models\MentorFeedback;
use App\Models\MentorTask;
use App\Models\SavedInternship;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionBaselineSeeder extends Seeder
{
    private const PASSWORD = 'Password123!';

    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            MasterDataSeeder::class,
            SuperAdminSeeder::class,
        ]);

        $superAdmin = $this->user('superadmin1@internhub.id', 'Default Super Admin', UserRole::SUPER_ADMIN);
        $admin = $this->user('admin1@internhub.id', 'Default Admin', UserRole::ADMIN);
        $hr = $this->user('hr@internhub.id', 'HR InternHub', UserRole::HR);
        $mentor = $this->user('mentor@internhub.id', 'Mentor InternHub', UserRole::MENTOR);
        $student = $this->user('student@internhub.id', 'Student InternHub', UserRole::USER);

        $this->profile($student);

        $company = Company::updateOrCreate(
            ['slug' => 'internhub-talent-lab'],
            [
                'name' => 'InternHub Talent Lab',
                'description' => 'Perusahaan demo untuk menampilkan alur rekrutmen magang di production.',
                'location' => 'Jakarta',
                'website' => 'https://internhub.id',
                'logo_url' => '/logo.png',
                'is_verified' => true,
            ],
        );

        $this->membership($hr, $company, CompanyRole::OWNER);
        $this->membership($mentor, $company, CompanyRole::MENTOR);

        $productInternship = Internship::updateOrCreate(
            ['slug' => 'product-design-intern-internhub'],
            [
                'company_id' => $company->id,
                'title' => 'Product Design Intern',
                'description' => 'Bantu tim produk merancang pengalaman pengguna untuk platform rekrutmen magang.',
                'requirements' => ['Figma', 'UX research', 'Portfolio design'],
                'benefits' => ['Mentoring mingguan', 'Sertifikat magang', 'Remote friendly'],
                'type' => 'Hybrid',
                'location' => 'Jakarta',
                'is_paid' => true,
                'stipend' => 'Rp 2.000.000 - 3.000.000',
                'deadline_at' => now()->addMonth(),
                'status' => 'published',
                'tags' => ['Figma', 'UI Design', 'UX Research'],
            ],
        );

        Internship::updateOrCreate(
            ['slug' => 'backend-laravel-intern-internhub'],
            [
                'company_id' => $company->id,
                'title' => 'Backend Laravel Intern',
                'description' => 'Bangun API dan workflow backend untuk platform internship modern.',
                'requirements' => ['PHP', 'Laravel', 'PostgreSQL', 'Git'],
                'benefits' => ['Code review', 'Project portfolio', 'Mentor teknis'],
                'type' => 'Remote',
                'location' => 'Remote',
                'is_paid' => true,
                'stipend' => 'Rp 2.500.000 - 4.000.000',
                'deadline_at' => now()->addWeeks(6),
                'status' => 'published',
                'tags' => ['Laravel', 'PHP', 'PostgreSQL'],
            ],
        );

        $application = Application::updateOrCreate(
            [
                'user_id' => $student->id,
                'internship_id' => $productInternship->id,
            ],
            [
                'status' => 'accepted',
                'cover_letter' => 'Saya tertarik membantu membangun pengalaman produk InternHub yang lebih baik.',
                'cv_snapshot' => 'private/cvs/student-internhub.pdf',
                'mentor_user_id' => $mentor->id,
                'interviewer_id' => $hr->id,
                'timeline' => [
                    [
                        'status' => 'accepted',
                        'label' => 'Lamaran Diterima',
                        'description' => 'Data baseline production untuk dashboard student, HR, dan mentor.',
                        'date' => now()->toDateTimeString(),
                    ],
                ],
            ],
        );

        SavedInternship::updateOrCreate([
            'user_id' => $student->id,
            'internship_id' => $productInternship->id,
        ]);

        MentorTask::updateOrCreate(
            [
                'application_id' => $application->id,
                'mentor_user_id' => $mentor->id,
                'title' => 'Review onboarding intern',
            ],
            [
                'description' => 'Pastikan peserta memahami target minggu pertama dan channel komunikasi.',
                'due_date' => now()->addWeek(),
                'status' => 'todo',
                'priority' => 2,
            ],
        );

        MentorFeedback::updateOrCreate(
            [
                'application_id' => $application->id,
                'mentor_user_id' => $mentor->id,
            ],
            [
                'content' => 'Baseline feedback untuk memastikan dashboard mentor menampilkan aktivitas terbaru.',
                'assessment' => [
                    'communication' => 4,
                    'initiative' => 4,
                    'technical_readiness' => 3,
                ],
                'status' => 'draft',
            ],
        );

        foreach ([$superAdmin, $admin, $hr, $mentor, $student] as $user) {
            $user->notifications()->updateOrCreate(
                [
                    'id' => $this->notificationId($user),
                ],
                [
                    'type' => 'production_baseline',
                    'data' => [
                        'title' => 'InternHub siap digunakan',
                        'message' => 'Data baseline sudah tersedia untuk landing page dan dashboard role.',
                    ],
                ],
            );
        }
    }

    private function user(string $email, string $name, UserRole $role): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::PASSWORD),
                'role' => $role->value,
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );

        $user->syncRoles([$role->value]);

        return $user;
    }

    private function profile(User $user): void
    {
        UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'bio' => 'Mahasiswa demo dengan profil lengkap untuk dashboard kandidat.',
                'phone_number' => '081234567890',
                'address' => 'Jakarta, Indonesia',
                'education' => [
                    [
                        'school' => 'Universitas InternHub',
                        'degree' => 'S1 Informatika',
                        'start_year' => 2022,
                        'end_year' => 2026,
                    ],
                ],
                'skills' => ['Laravel', 'Vue', 'Figma'],
                'cv_path' => 'private/cvs/student-internhub.pdf',
            ],
        );
    }

    private function membership(User $user, Company $company, CompanyRole $role): void
    {
        $user->companyMemberships()->updateOrCreate(
            ['company_id' => $company->id],
            [
                'role' => $role->value,
                'is_active' => true,
            ],
        );
    }

    private function notificationId(User $user): string
    {
        $hash = md5('production-baseline-'.$user->email);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            substr($hash, 12, 4),
            substr($hash, 16, 4),
            substr($hash, 20, 12),
        );
    }
}
