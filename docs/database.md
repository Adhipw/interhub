# Database Design - InternHub

## 1. Database Baru: `internhub_rekrutmen_2026`
Proyek ini wajib diisolasi dari database lama untuk mencegah kerusakan data dan memastikan performa optimal.

## 2. Tabel Utama
- `users`: Data autentikasi dasar.
- `user_profiles`: Data diri mahasiswa (biodata, edukasi, skill).
- `social_accounts`: Pemetaan Google OAuth.
- `email_verification_otps`: Penyimpanan hash OTP 6 digit via Resend.
- `companies`: Profil perusahaan penyedia magang.
- `company_members`: Role user di dalam perusahaan (Owner, HR, Mentor).
- `internships`: Lowongan magang yang dibuka.
- `internship_applications`: Data lamaran masuk.
- `recruitment_stages`: Tahapan rekrutmen kustom (e.g., CV Screening, Interview).
- `mentors`: Data pembimbing magang.
- `attendance_records`: Absensi harian berbasis geolocation.
- `audit_logs`: Pencatatan aktivitas sensitif (e.g., perubahan role, akses dokumen private).

## 3. Aturan Migration
- Selalu tampilkan `DB_DATABASE` sebelum eksekusi.
- Gunakan `JSONB` untuk data dinamis seperti `metadata` lamaran atau `settings` profil.
- Indeks wajib pada kolom `email`, `company_id`, dan `status`.
