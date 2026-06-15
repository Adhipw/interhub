<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // System Settings
        SystemSetting::updateOrCreate(['key' => 'site_name'], [
            'value' => 'InternHub',
            'group' => 'general',
            'type' => 'string',
            'description' => 'Nama platform utama.',
        ]);

        SystemSetting::updateOrCreate(['key' => 'max_upload_size'], [
            'value' => '5120',
            'group' => 'system',
            'type' => 'integer',
            'description' => 'Batas upload file dalam KB.',
        ]);

        SystemSetting::updateOrCreate(['key' => 'google_client_secret'], [
            'value' => 'dummy_secret_key_123',
            'group' => 'integration',
            'type' => 'string',
            'description' => 'Secret key untuk integrasi Google OAuth.',
            'is_sensitive' => true,
        ]);

        // Feature Flags
        FeatureFlag::updateOrCreate(['key' => 'enable_mentoring'], [
            'name' => 'Mentoring Module',
            'is_enabled' => true,
            'description' => 'Aktifkan fitur mentoring untuk mentor dan mahasiswa.',
        ]);

        FeatureFlag::updateOrCreate(['key' => 'enable_google_login'], [
            'name' => 'Google Login',
            'is_enabled' => false,
            'description' => 'Izinkan pengguna login menggunakan akun Google.',
        ]);

        FeatureFlag::updateOrCreate(['key' => 'maintenance_mode'], [
            'name' => 'Maintenance Mode',
            'is_enabled' => false,
            'description' => 'Aktifkan mode pemeliharaan untuk seluruh platform.',
        ]);
    }
}
