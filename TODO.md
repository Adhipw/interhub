# TODO - reCAPTCHA Railway Fix

- [x] Update frontend login captcha gating agar bypass hanya untuk environment lokal/dev (`frontend/resources/js/Pages/Auth/Login.vue`).
- [x] Update backend login request agar rule captcha tidak hardcoded bypass (`backend/app/Http/Requests/Auth/LoginRequest.php`).
- [x] Harden captcha verification rule untuk Railway (gunakan config, validasi secret key, fallback explicit-only) (`backend/app/Rules/CaptchaRule.php`).
- [x] Set default config reCAPTCHA fallback lebih aman untuk production (`backend/config/services.php`).
- [x] Jalankan verifikasi cepat sintaks PHP untuk file backend yang diubah.
- [x] Review hasil akhir agar pesan error captcha tampil benar di UI.

# TODO - Railway refresh/database follow-up

- [x] Cegah API publik mengirim user ke `/login` saat response `401/419` sehingga tidak terlihat seperti refresh/redirect sendiri (`frontend/resources/js/Services/api.ts`).
- [x] Tambahkan validasi startup Railway untuk `APP_KEY`, `APP_URL`, dan `DB_CONNECTION` agar deploy gagal dengan pesan jelas jika env production salah (`docker/railway-start.sh`).
- [x] Tambahkan ringkasan database non-secret saat startup Railway lewat `php artisan db:show --counts` (`docker/railway-start.sh`).
- [x] Tambahkan healthcheck stats untuk membedakan database kosong/baru dari database lama (`backend/app/Http/Controllers/Api/ApiHealthCheckController.php`).
- [x] Dokumentasikan pengecekan Railway env, healthcheck, dan database mismatch (`docs/railway-deployment.md`).
- [x] Verifikasi PHP lint, Laravel route health, dan frontend production build.
- [x] Kunci `E2eNavigationSeeder` dan Playwright agar data E2E tidak bisa nyasar ke database production (`backend/database/seeders/E2eNavigationSeeder.php`, `frontend/playwright.config.ts`).
- [x] Tambahkan baseline seeder opsional untuk landing page dan dashboard 5 role (`backend/database/seeders/ProductionBaselineSeeder.php`, `docker/railway-start.sh`).
- [ ] Restore/import data lama Railway jika memang database production saat ini berbeda. Ini butuh backup dump lama atau akses Railway PostgreSQL lama; repo lokal tidak berisi data production lama.
