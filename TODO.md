# TODO - reCAPTCHA Railway Fix

- [x] Update frontend login captcha gating agar bypass hanya untuk environment lokal/dev (`frontend/resources/js/Pages/Auth/Login.vue`).
- [x] Update backend login request agar rule captcha tidak hardcoded bypass (`backend/app/Http/Requests/Auth/LoginRequest.php`).
- [x] Harden captcha verification rule untuk Railway (gunakan config, validasi secret key, fallback explicit-only) (`backend/app/Rules/CaptchaRule.php`).
- [x] Set default config reCAPTCHA fallback lebih aman untuk production (`backend/config/services.php`).
- [x] Jalankan verifikasi cepat sintaks PHP untuk file backend yang diubah.
- [x] Review hasil akhir agar pesan error captcha tampil benar di UI.
