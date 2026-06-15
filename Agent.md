# PROMPT FINAL — INTERNHUB FULL WEB SERVICE

Dokumen ini adalah prompt final utama untuk membangun project **InternHub Rekrutmen Magang** di Antigravity secara bertahap, aman, rapi, tidak lompat alur, memakai database baru, memakai struktur folder terpisah, dan dibuat sebagai **full web service production-ready**, bukan sekadar website tampilan atau CRUD sederhana.

Project ini memakai pendekatan **hybrid architecture**:

* **Laravel backend** di `backend/`
* **Vue 3 + Inertia + TypeScript + Tailwind frontend** di `frontend/`
* **REST API `/api/v1`** untuk service layer, integrasi, dashboard async action, upload, search, notification, mobile-ready endpoint, reporting, health check, dan future external clients
* **Docker, PostgreSQL, Redis, Nginx, Supervisor** di `infrastructure/`
* **Dokumentasi** di `docs/`
* **Script helper** di `scripts/`

---

# 0. CARA PAKAI UNTUK USER

Gunakan prompt ini bertahap. Jangan copy semua batch sekaligus.

```text
1. Copy PROMPT 1 - MISSION PROMPT ke Antigravity.
2. Tunggu agent membuat .agents/rules.
3. Copy BATCH 0.
4. Setelah selesai, copy REVIEW BATCH 0.
5. Kalau sudah aman, kirim: PRD disetujui, lanjut Batch 1.
6. Copy ENVIRONMENT DETECTION.
7. Copy BATCH 1.
8. Setelah selesai, copy REVIEW BATCH 1.
9. Kalau sudah aman, kirim: Batch 1 disetujui, lanjut Batch 2.
10. Ulangi pola batch dan review sampai Batch 25.
```

Aturan penting:

* Jangan lompat batch.
* Jangan lanjut jika review belum aman.
* Kalau ada error, gunakan **PROMPT RECOVERY**.
* Kalau batch punya frontend, gunakan **PROMPT UI/UX WORLD-CLASS REVIEW**.
* Kalau batch menyentuh data sensitif, auth, role, file, lokasi, AI, database, API, queue, scheduler, atau integrasi, gunakan **PROMPT SECURITY REVIEW**.
* Kalau batch menyentuh migration atau database, pastikan database aktif adalah database baru project ini.
* Jangan menjalankan `migrate:fresh`, `db:wipe`, `drop database`, `truncate`, atau command destruktif tanpa approval.
* Jangan menjalankan `php artisan` dari root project.
* Jangan menjalankan `npm` dari folder `backend/`.
* Jangan menjalankan `composer` dari folder `frontend/`.
* Selalu jalankan command dari folder yang benar.

---

# 1. PROMPT 1 — MISSION PROMPT

Copy prompt ini paling pertama ke Antigravity.

```text
Anda adalah Antigravity Agent untuk project InternHub.

Project ini adalah aplikasi web fullstack rekrutmen magang bernama InternHub.

Saya orang awam, jadi Anda wajib bekerja pelan-pelan, terstruktur, aman, dan tidak langsung coding semua fitur.

Tujuan utama:
Membangun aplikasi rekrutmen magang production-ready, layak untuk skripsi, rapi secara arsitektur, aman secara security, memakai database baru yang bersih, memakai struktur folder yang mudah dipahami, memiliki UI/UX kelas dunia untuk standar 2026, dan dibuat sebagai full web service production-ready.

Project ini harus terlihat seperti website rekrutmen magang sungguhan, bukan CRUD demo, bukan template admin lama, bukan landing page kosong, dan bukan tampilan yang kelihatan AI banget.

Project ini juga harus benar-benar full web service, bukan hanya website tampilan.

Gunakan pendekatan hybrid:

1. Inertia + Vue untuk pengalaman website utama.
2. REST API /api/v1 untuk service layer, integrasi, mobile-ready endpoint, dashboard async actions, search, notification, upload file, reporting, health check, dan future external clients.

Jangan hanya membuat halaman Blade/Vue. Setiap fitur utama harus punya:

1. Web page jika fitur terlihat oleh user.
2. Backend service/action.
3. API endpoint jika relevan.
4. Form Request validation.
5. Policy/Gate authorization.
6. API Resource response jika endpoint API.
7. Activity log jika aktivitas penting.
8. Audit log jika aksi sensitif.
9. Security event jika aksi berisiko.
10. Test.
11. Dokumentasi.

Stack utama:

- Laravel 13
- PHP 8.3 atau 8.4
- Laravel Sanctum session authentication
- Laravel Socialite untuk Google login/register
- Laravel Horizon
- Laravel Reverb
- Laravel Queue
- Laravel Scheduler
- Laravel Notification
- Laravel Mail
- Inertia.js
- Vue 3
- TypeScript
- Tailwind CSS 4
- PostgreSQL
- Redis
- Docker Compose
- Pest Testing
- Leaflet.js
- OpenStreetMap
- Haversine formula
- Cloudflare R2 untuk production storage
- MinIO atau local storage untuk development
- Resend untuk email verification OTP, forgot password, reset password, dan email notification
- Local AI / Gemini optional dengan provider abstraction

DATABASE BARU WAJIB:

Project kali ini wajib memakai database baru:

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=internhub_rekrutmen_2026
DB_USERNAME=internhub_user
DB_PASSWORD=internhub_password

Aturan database:

1. Jangan memakai database lama.
2. Jangan menjalankan migration ke database lama.
3. Jangan drop database lama.
4. Jangan truncate database lama.
5. Jangan menjalankan migrate:fresh tanpa approval.
6. Jangan menjalankan db:wipe tanpa approval.
7. Sebelum menjalankan migration, tampilkan database aktif.
8. Database aktif harus bernama internhub_rekrutmen_2026.
9. Jika database belum ada, berikan instruksi pembuatan database baru tanpa menghapus database lama.
10. Semua tabel project ini harus masuk ke database baru tersebut.

STRUKTUR FOLDER WAJIB:

Project InternHub wajib memakai struktur folder terpisah agar saya tidak bingung mencari file.

Gunakan struktur utama:

internhub/
├── backend/
├── frontend/
├── infrastructure/
├── docs/
├── scripts/
├── .agents/
├── README.md
├── PRD.md
├── Architecture.md
└── .gitignore

Aturan struktur folder:

1. Laravel backend wajib berada di backend/.
2. Vue 3, Inertia, TypeScript, Tailwind frontend wajib berada di frontend/.
3. Docker, Nginx, PostgreSQL, Redis, Supervisor, dan server config wajib berada di infrastructure/.
4. Dokumentasi wajib berada di docs/.
5. Script pembantu wajib berada di scripts/.
6. Rules Antigravity wajib berada di .agents/rules/.
7. Jangan mencampur Controller Laravel ke frontend/.
8. Jangan mencampur Vue component ke backend/app.
9. Jangan membuat file sembarangan di root.
10. Jangan menjalankan php artisan dari root internhub/.
11. Jangan menjalankan composer dari frontend/.
12. Jangan menjalankan npm dari backend/.
13. Jangan menjalankan docker compose dari folder yang salah.

Command path rules:

Backend command:
cd backend
php artisan ...
composer ...

Frontend command:
cd frontend
npm ...

Docker command:
cd infrastructure/docker
docker compose ...

FULL WEB SERVICE MODE WAJIB:

Definisi full web service untuk InternHub:

1. Aplikasi harus memiliki frontend website yang bisa digunakan user.
2. Aplikasi harus memiliki backend service yang rapi dan modular.
3. Aplikasi harus memiliki REST API internal menggunakan prefix /api/v1.
4. Semua business logic utama wajib berada di service/action layer, bukan di controller.
5. Controller hanya menerima request, validasi, authorize, memanggil action/service, lalu mengembalikan response.
6. Aplikasi harus punya API response standard.
7. Aplikasi harus punya error response standard.
8. Aplikasi harus punya health check endpoint.
9. Aplikasi harus punya queue worker untuk proses berat.
10. Aplikasi harus punya scheduler untuk proses berkala.
11. Aplikasi harus punya notification service.
12. Aplikasi harus punya file storage service.
13. Aplikasi harus punya audit log, activity log, dan security event.
14. Aplikasi harus punya API documentation.
15. Aplikasi harus punya OpenAPI spec atau Postman collection.
16. Aplikasi harus punya environment configuration yang jelas.
17. Aplikasi harus bisa dijalankan secara lokal, LAN/WiFi, Docker, dan siap deployment.
18. Aplikasi harus punya testing untuk endpoint dan service penting.

Arsitektur backend wajib mengikuti pola:

Route -> Controller -> Form Request -> Policy/Gate -> Action/Service -> DTO -> Model -> Event/Job/Notification -> Activity/Audit/Security Log jika relevan.

Struktur service backend wajib disiapkan:

backend/app/
├── Actions/
├── Services/
├── DTOs/
├── QueryFilters/
├── Enums/
├── Policies/
├── Events/
├── Listeners/
├── Jobs/
├── Notifications/
├── Http/
│   ├── Controllers/
│   │   ├── Web/
│   │   └── Api/
│   ├── Requests/
│   ├── Resources/
│   └── Middleware/

REST API wajib menggunakan prefix:

/api/v1

Success API response wajib standar:

{
  "success": true,
  "message": "Berhasil",
  "data": {},
  "meta": {}
}

Error API response wajib standar:

{
  "success": false,
  "message": "Terjadi kesalahan",
  "errors": {},
  "request_id": "..."
}

Endpoint minimal yang wajib dibuat selama project:

Auth API:
- POST /api/v1/auth/login
- POST /api/v1/auth/register
- POST /api/v1/auth/logout
- POST /api/v1/auth/forgot-password
- POST /api/v1/auth/reset-password
- POST /api/v1/auth/email/verify-otp
- POST /api/v1/auth/email/resend-otp
- GET /api/v1/auth/me

Public API:
- GET /api/v1/public/internships
- GET /api/v1/public/internships/{slug}
- GET /api/v1/public/companies/{slug}
- GET /api/v1/public/search/internships
- GET /api/v1/public/nearby-internships

Candidate API:
- GET /api/v1/candidate/dashboard
- GET /api/v1/candidate/profile
- PUT /api/v1/candidate/profile
- POST /api/v1/candidate/documents
- GET /api/v1/candidate/documents
- DELETE /api/v1/candidate/documents/{id}
- POST /api/v1/candidate/internships/{id}/apply
- GET /api/v1/candidate/applications
- GET /api/v1/candidate/applications/{id}
- POST /api/v1/candidate/saved-internships/{id}
- DELETE /api/v1/candidate/saved-internships/{id}

HR API:
- GET /api/v1/hr/dashboard
- GET /api/v1/hr/internships
- POST /api/v1/hr/internships
- PUT /api/v1/hr/internships/{id}
- DELETE /api/v1/hr/internships/{id}
- GET /api/v1/hr/applications
- GET /api/v1/hr/applications/{id}
- PATCH /api/v1/hr/applications/{id}/status
- POST /api/v1/hr/applications/{id}/notes
- POST /api/v1/hr/applications/{id}/interviews
- GET /api/v1/hr/reports/recruitment

Mentor API:
- GET /api/v1/mentor/dashboard
- GET /api/v1/mentor/mentees
- GET /api/v1/mentor/mentees/{id}
- POST /api/v1/mentor/tasks
- POST /api/v1/mentor/feedback
- POST /api/v1/mentor/evaluations

Admin API:
- GET /api/v1/admin/dashboard
- GET /api/v1/admin/users
- PATCH /api/v1/admin/users/{id}/role
- GET /api/v1/admin/companies
- GET /api/v1/admin/audit-logs
- GET /api/v1/admin/security-events
- GET /api/v1/admin/system/health

System API:
- GET /api/v1/health
- GET /api/v1/version
- GET /api/v1/status
- GET /api/v1/features
- GET /api/v1/notifications
- PATCH /api/v1/notifications/{id}/read

Full web service requirements:

1. Buat ApiResponse helper.
2. Buat ApiExceptionHandler atau standard error renderer.
3. Buat RequestIdMiddleware.
4. Buat API Resources untuk response JSON.
5. Buat Form Request untuk semua mutasi data.
6. Buat Policy/Gate untuk semua endpoint sensitif.
7. Buat rate limit per endpoint penting.
8. Buat API documentation di docs/api.md.
9. Buat OpenAPI spec di docs/openapi.yaml.
10. Buat health check yang memeriksa app status, database, Redis, queue, storage, dan mail config tanpa membocorkan secret.
11. Buat queue jobs untuk email OTP, notification, upload document processing, report generation, external sync, dan AI processing jika aktif.
12. Buat scheduler untuk menutup lowongan expired, membersihkan OTP expired, membersihkan temporary file, reminder interview, dan summary notification.
13. Buat service abstraction: AuthService, OtpService, InternshipService, ApplicationService, DocumentService, NotificationService, AuditLogService, ActivityLogService, SecurityEventService, SearchService, NearbyInternshipService, AttendanceService, AiService, IntegrationService.
14. Buat tests untuk API auth, authorization, validation, response format, public internship API, candidate application API, HR application status API, document upload API, notification API, dan health check API.

Aturan utama:

1. Jangan langsung membuat aplikasi lengkap.
2. Jangan langsung install package.
3. Jangan langsung menjalankan composer.
4. Jangan langsung menjalankan npm.
5. Jangan langsung menjalankan php artisan.
6. Jangan langsung membuat migration, controller, model, atau Vue page.
7. Mulai hanya dari Batch 0 - PRD & Foundation.
8. Batch 0 hanya boleh membuat dokumentasi.
9. Setelah Batch 0 selesai, berhenti dan tunggu review saya.
10. Jangan lanjut Batch 1 sebelum saya menulis: PRD disetujui, lanjut Batch 1.
11. Setelah setiap batch, wajib membuat batch report.
12. Setelah setiap batch, berhenti dan tunggu review saya.

UI/UX dan design system wajib kelas dunia untuk standar 2026:

- premium
- sophisticated
- highly-polished
- modern
- clean
- responsive
- accessible
- enterprise-grade
- youth-friendly
- professional
- trust-building
- human-made
- natural
- tidak terlihat AI-generated
- tidak seperti template admin lama
- tidak seperti CRUD demo sederhana
- tidak seperti landing page generik

Aturan anti AI-looking UI:

1. Jangan membuat tampilan terlalu AI banget.
2. Jangan memakai gradient ungu/biru neon berlebihan.
3. Jangan memakai glassmorphism berlebihan.
4. Jangan memakai blob abstrak berlebihan.
5. Jangan memakai ilustrasi 3D generik yang sering terlihat seperti hasil AI.
6. Jangan memakai copywriting bombastis seperti “revolusi masa depan karier”.
7. Jangan membuat testimonial palsu.
8. Jangan membuat angka statistik palsu.
9. Jangan membuat logo perusahaan palsu.
10. Jangan membuat semua card terlalu seragam seperti template SaaS generik.
11. Jangan membuat dashboard seperti template admin lama.
12. Desain harus terasa natural, manusiawi, profesional, dan dipercaya.

Homepage publik wajib terlihat seperti website rekrutmen magang sungguhan:

1. Tambahkan hero image/foto/visual bertema magang, interview, mentoring, HR review, intern bekerja, atau suasana kerja profesional.
2. Homepage tidak boleh terasa seperti website kosong, template admin, CRUD demo, landing page generik, atau website yang meragukan.
3. Copywriting harus natural, manusiawi, profesional, dan meyakinkan.
4. Bangun trust melalui struktur informasi, visual, lowongan, alur melamar, tracking lamaran, dan penjelasan fitur yang jelas.
5. Jangan memakai klaim palsu atau testimoni palsu.
6. Jangan menulis “bukan scam” secara frontal; bangun rasa percaya lewat desain dan informasi yang kredibel.
7. Tambahkan animasi halus saat halaman dibuka, scroll, hover tombol, hover card, buka modal/drawer, dan submit form.
8. Motion harus premium, ringan, cepat, dan menghormati prefers-reduced-motion.

Auth UI harus mengikuti pola modern:

Login page:
1. Input Email
2. Input Password
3. Toggle show/hide password
4. Link Lupa password?
5. CAPTCHA
6. Tombol utama Masuk
7. Divider Atau
8. Tombol Masuk dengan Google
9. Link Baru di InternHub? Daftar di sini

Register page:
1. Judul besar modern
2. Tombol Daftar dengan Google di atas
3. Divider Atau
4. Input Nama Lengkap
5. Input No HP dengan kode negara
6. Input Email
7. Input Password
8. Password strength meter
9. Input Konfirmasi Password
10. Checkbox persetujuan data
11. Tombol Daftar
12. Link Sudah mempunyai akun? Login di sini

Aturan path dan command:

Sebelum menjalankan command development apa pun, Anda wajib membaca dan melaporkan path:

- Root project
- Folder aktif
- PHP
- Composer
- Node.js
- npm
- Git
- artisan jika tersedia
- backend/artisan jika tersedia
- backend/composer.json jika tersedia
- frontend/package.json jika tersedia
- frontend/vite.config.ts jika tersedia
- infrastructure/docker/docker-compose.yml jika tersedia
- database connection jika .env tersedia
- database name yang sedang aktif

Jangan mengasumsikan path runtime.
Jangan mengasumsikan database aktif.
Jangan mengasumsikan folder aktif.

Jika ada error, jangan ulang command membabi buta. Diagnosis dulu dan jelaskan ke saya dengan bahasa sederhana.

Sekarang tugas pertama Anda:
Buat folder .agents/rules dan isi rules project agar semua pekerjaan berikutnya mengikuti aturan InternHub.
```

---

# 2. PROJECT RULES WAJIB UNTUK `.agents/rules`

Copy setelah Mission Prompt.

```text
Buat folder .agents/rules dan file rules berikut:

1. .agents/rules/01-project-scope.md
2. .agents/rules/02-batch-workflow.md
3. .agents/rules/03-terminal-safety.md
4. .agents/rules/04-laravel-architecture.md
5. .agents/rules/05-auth-google-resend.md
6. .agents/rules/06-uiux-world-class.md
7. .agents/rules/07-security-privacy.md
8. .agents/rules/08-ai-rules.md
9. .agents/rules/09-location-nearby-rules.md
10. .agents/rules/10-batch-report.md
11. .agents/rules/11-database-new-project.md
12. .agents/rules/12-anti-ai-looking-ui.md
13. .agents/rules/13-project-folder-structure.md
14. .agents/rules/14-full-web-service-api.md
15. .agents/rules/15-queue-scheduler-healthcheck.md

Isi rules wajib:

- Jangan lompat batch.
- Jangan coding sebelum Batch 0 disetujui.
- Jangan menjalankan command destruktif tanpa approval.
- Jangan menjalankan artisan jika tidak berada di backend/.
- Jangan menjalankan npm jika tidak berada di frontend/.
- Jangan menjalankan composer jika tidak berada di backend/.
- Jangan menjalankan docker compose jika tidak berada di infrastructure/docker/.
- Deteksi path PHP, Composer, Node.js, npm, Git sebelum command development.
- Deteksi struktur folder sebelum setup.
- Deteksi database aktif sebelum migration.
- Database project ini wajib internhub_rekrutmen_2026.
- Jangan memakai database lama.
- Jangan menjalankan migrate:fresh tanpa approval.
- Jangan menjalankan db:wipe tanpa approval.
- Jangan drop/truncate database tanpa approval.
- Backend Laravel wajib berada di backend/.
- Frontend Vue/Inertia/Tailwind wajib berada di frontend/.
- Infrastructure wajib berada di infrastructure/.
- Dokumentasi wajib berada di docs/.
- Script pembantu wajib berada di scripts/.
- Backend flow wajib: Route -> Controller -> Form Request -> Policy/Gate -> Action/Service -> DTO -> Model -> Event/Job/Notification.
- Controller harus tipis.
- Business logic masuk Action, Service, DTO, Job, Listener, Policy, QueryFilter, atau Domain Service.
- Project wajib punya REST API /api/v1 untuk fitur service yang relevan.
- Project wajib punya ApiResponse helper.
- Project wajib punya RequestIdMiddleware.
- Project wajib punya standard success response dan error response.
- Project wajib punya API Resource untuk response JSON.
- Project wajib punya docs/api.md dan docs/openapi.yaml.
- Project wajib punya health check endpoint.
- Project wajib punya queue worker untuk proses berat.
- Project wajib punya scheduler untuk proses berkala.
- Auth memakai Laravel Sanctum session authentication.
- Google login/register memakai Laravel Socialite.
- Email verification, forgot password, reset password, dan email notification memakai Resend.
- Email verification utama wajib memakai OTP 6 digit via Resend.
- RESEND_API_KEY hanya boleh di backend .env.
- Jangan expose secret ke frontend.
- Jangan log secret/token/password/API key.
- UI/UX harus premium dan kelas dunia.
- UI/UX tidak boleh terlihat AI-generated.
- Jangan memakai testimonial palsu.
- Jangan memakai angka statistik palsu.
- Jangan memakai logo perusahaan palsu.
- Semua mutasi data wajib Form Request.
- Semua akses sensitif wajib Policy/Gate.
- Semua aksi sensitif wajib audit log.
- Semua aktivitas penting wajib activity log.
- Semua security event wajib dicatat.
- File kandidat wajib private.
- Lokasi wajib consent-based.
- Realtime location hanya saat attendance session.
- Nearby search tidak boleh membocorkan lokasi user.
- AI assists, human decides.
- AI tidak boleh mengambil keputusan final.
- Setiap batch wajib punya batch report.
```

---

# 3. STRUKTUR FOLDER FINAL

```text
internhub/
├── backend/
│   ├── app/
│   │   ├── Actions/
│   │   ├── DTOs/
│   │   ├── Enums/
│   │   ├── Events/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Api/
│   │   │   │   └── Web/
│   │   │   ├── Middleware/
│   │   │   ├── Requests/
│   │   │   └── Resources/
│   │   ├── Jobs/
│   │   ├── Listeners/
│   │   ├── Models/
│   │   ├── Notifications/
│   │   ├── Policies/
│   │   ├── QueryFilters/
│   │   └── Services/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   │   └── views/
│   │       └── app.blade.php
│   ├── routes/
│   │   ├── web.php
│   │   ├── api.php
│   │   ├── console.php
│   │   └── channels.php
│   ├── storage/
│   ├── tests/
│   │   ├── Feature/
│   │   │   ├── Api/
│   │   │   └── Web/
│   │   └── Unit/
│   ├── artisan
│   ├── composer.json
│   ├── composer.lock
│   ├── phpunit.xml
│   ├── pint.json
│   ├── phpstan.neon
│   ├── .env
│   └── .env.example
│
├── frontend/
│   ├── resources/
│   │   ├── css/
│   │   │   └── app.css
│   │   └── js/
│   │       ├── app.ts
│   │       ├── bootstrap.ts
│   │       ├── Pages/
│   │       ├── Components/
│   │       ├── Layouts/
│   │       ├── Composables/
│   │       ├── Stores/
│   │       ├── Types/
│   │       ├── Services/
│   │       ├── Constants/
│   │       └── Lib/
│   ├── package.json
│   ├── package-lock.json
│   ├── tsconfig.json
│   ├── vite.config.ts
│   ├── eslint.config.js
│   ├── prettier.config.js
│   └── tailwind.config.ts
│
├── infrastructure/
│   ├── docker/
│   │   ├── docker-compose.yml
│   │   ├── Dockerfile.backend
│   │   ├── Dockerfile.frontend
│   │   └── Dockerfile.queue
│   ├── nginx/
│   │   └── default.conf
│   ├── postgres/
│   │   └── init/
│   ├── redis/
│   │   └── redis.conf
│   └── supervisor/
│       └── horizon.conf
│
├── docs/
│   ├── adr/
│   ├── batch-reports/
│   ├── api.md
│   ├── openapi.yaml
│   ├── web-service-architecture.md
│   ├── api-response-standard.md
│   ├── health-check.md
│   ├── queue-scheduler.md
│   ├── database.md
│   ├── database-new-project.md
│   ├── project-structure.md
│   ├── design-system.md
│   ├── anti-ai-looking-ui.md
│   ├── homepage-public-experience.md
│   ├── local-network-demo.md
│   ├── email.md
│   ├── security.md
│   └── testing.md
│
├── scripts/
│   ├── dev/
│   ├── setup/
│   ├── database/
│   ├── quality/
│   └── deploy/
│
├── .agents/
│   └── rules/
│
├── README.md
├── PRD.md
├── Architecture.md
└── .gitignore
```

Aturan backend:

* Folder `backend/` hanya untuk Laravel.
* `artisan` hanya boleh dijalankan dari `backend/`.
* `composer` hanya boleh dijalankan dari `backend/`.
* Controller Web berada di `backend/app/Http/Controllers/Web`.
* Controller API berada di `backend/app/Http/Controllers/Api`.
* API Resource berada di `backend/app/Http/Resources`.
* Action berada di `backend/app/Actions`.
* Service berada di `backend/app/Services`.
* DTO berada di `backend/app/DTOs`.
* Model berada di `backend/app/Models`.
* Migration berada di `backend/database/migrations`.
* Routes berada di `backend/routes`.

Aturan frontend:

* Folder `frontend/` hanya untuk Vue 3, Inertia, TypeScript, Tailwind, dan frontend tooling.
* `npm` hanya boleh dijalankan dari `frontend/`.
* Vue Pages berada di `frontend/resources/js/Pages`.
* Vue Components berada di `frontend/resources/js/Components`.
* Layout berada di `frontend/resources/js/Layouts`.
* CSS/Tailwind berada di `frontend/resources/css`.
* Vite config berada di `frontend/vite.config.ts`.

Aturan Inertia:

1. Laravel backend tetap berada di `backend/`.
2. Vue/Inertia source berada di `frontend/`.
3. Laravel tetap menyajikan Inertia response dari Controller backend.
4. Root Blade `app.blade.php` tetap berada di `backend/resources/views/app.blade.php`.
5. Build output frontend diarahkan ke `backend/public/build`.
6. Jangan mengubah arsitektur menjadi API-only + SPA kecuali saya minta.
7. Jangan mengubah Vue ke React, Next, atau Nuxt.

Aturan REST API:

1. REST API berada di `backend/routes/api.php`.
2. Semua endpoint API memakai prefix `/api/v1`.
3. Semua response API memakai format standar.
4. Semua error API memakai `request_id`.
5. Semua endpoint sensitif memakai Policy/Gate.
6. Semua mutasi memakai Form Request.
7. Semua response entity memakai API Resource.
8. Semua API penting wajib punya test.

---

# 4. DATABASE BARU PROJECT INI

```text
DATABASE BARU INTERNHUB

Project ini wajib memakai database baru:

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=internhub_rekrutmen_2026
DB_USERNAME=internhub_user
DB_PASSWORD=internhub_password

Jika menggunakan Docker Compose:

POSTGRES_DB=internhub_rekrutmen_2026
POSTGRES_USER=internhub_user
POSTGRES_PASSWORD=internhub_password

Aturan:

1. Database lama tidak boleh dipakai.
2. Database lama tidak boleh dihapus.
3. Database lama tidak boleh di-reset.
4. Jangan menjalankan migration sebelum memastikan database aktif.
5. Jangan menjalankan php artisan migrate sebelum menampilkan DB_DATABASE.
6. Jangan menjalankan migrate:fresh tanpa approval eksplisit.
7. Jangan menjalankan db:wipe tanpa approval eksplisit.
8. Jangan menjalankan drop database.
9. Jangan menjalankan truncate table.
10. Jika database belum ada, buat instruksi pembuatan database baru, bukan memakai database lama.
11. Semua dokumentasi database harus menyebut database baru internhub_rekrutmen_2026.
12. Semua batch report yang menyentuh database wajib menampilkan database aktif.
```

Tabel utama yang harus direncanakan:

* users
* user_profiles
* social_accounts
* email_verification_otps
* password_reset_otps
* companies
* company_members
* internships
* internship_locations
* internship_applications
* application_documents
* saved_internships
* recruitment_pipelines
* recruitment_stages
* application_stage_histories
* mentors
* mentor_assignments
* mentor_tasks
* mentor_feedback
* evaluations
* activity_logs
* audit_logs
* security_events
* login_attempts
* user_sessions
* notifications
* integration_sources
* integration_logs
* nearby_search_logs
* attendance_sessions
* attendance_records
* live_location_snapshots
* document_access_logs
* ai_usage_logs
* feature_flags
* system_settings

---

# 5. RESEND EMAIL RULES

```text
KONFIGURASI EMAIL PROVIDER - RESEND

Gunakan Resend untuk:

1. Email verification OTP 6 digit.
2. Forgot password.
3. Reset password.
4. Notification email penting.

Aturan Resend:

1. API key Resend hanya boleh disimpan di backend .env.
2. Jangan expose RESEND_API_KEY ke frontend.
3. Jangan hardcode API key di kode.
4. Jangan mencatat API key ke log.
5. Gunakan Laravel Notification/Mail agar provider email bisa diganti di masa depan.
6. Email verification utama wajib memakai OTP 6 digit.
7. Forgot password boleh memakai secure reset link dan OTP fallback.
8. Untuk local development, boleh siapkan fallback Mailpit.
9. Dokumentasikan cara membuat API key Resend dan verifikasi domain/sender di docs/email.md.
10. Hapus konfigurasi mail default lama berikut dari .env.example:
   - MAIL_MAILER=log
   - MAIL_SCHEME=null
   - MAIL_HOST=127.0.0.1
   - MAIL_PORT=2525
   - MAIL_USERNAME=null
   - MAIL_PASSWORD=null
   - MAIL_FROM_ADDRESS="hello@example.com"
   - MAIL_FROM_NAME="${APP_NAME}"

Gunakan .env.example berikut:

MAIL_MAILER=resend
MAIL_FROM_ADDRESS=no-reply@internhub.test
MAIL_FROM_NAME="InternHub"

RESEND_API_KEY=
RESEND_FROM_ADDRESS=no-reply@internhub.test
RESEND_FROM_NAME="InternHub"

# Optional local fallback
MAIL_FALLBACK_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

---

# 6. INSTALL LARAVEL DENGAN STRUKTUR FOLDER TERPISAH

```text
INSTALL LARAVEL INTERNHUB DENGAN STRUKTUR FOLDER TERPISAH

Buat struktur project seperti ini:

internhub/
├── backend/
├── frontend/
├── infrastructure/
├── docs/
├── scripts/
└── .agents/

Langkah aman:

1. Buat root folder:

mkdir internhub
cd internhub

2. Install Laravel di folder backend:

composer create-project laravel/laravel backend

3. Masuk ke backend untuk cek Laravel:

cd backend
php artisan --version

4. Kembali ke root lalu buat folder frontend, infrastructure, docs, scripts, dan .agents:

cd ..
mkdir frontend
mkdir infrastructure
mkdir docs
mkdir scripts
mkdir -p .agents/rules

5. Jangan menjalankan migration dulu.
6. Jangan setup database lama.
7. Database project ini wajib:

DB_DATABASE=internhub_rekrutmen_2026

8. Setelah Laravel berhasil dibuat, semua command backend harus dari folder backend.
9. Semua command frontend nanti harus dari folder frontend.
10. Semua command docker nanti harus dari folder infrastructure/docker.

Output wajib:

- Struktur folder yang dibuat
- Command yang dijalankan
- Laravel version
- Lokasi backend
- Lokasi frontend
- Lokasi docs
- Lokasi infrastructure
- Lokasi scripts
- Status akhir
- Berhenti dan tunggu review saya
```

---

# 7. PROMPT ENVIRONMENT DETECTION

```text
Lakukan environment detection terlebih dahulu.

Saya orang awam, jadi jelaskan hasilnya dengan bahasa sederhana.

Jangan install package.
Jangan mengubah PATH.
Jangan menjalankan artisan command yang mengubah state.
Jangan menjalankan npm install kecuali batch memang mengizinkan.
Jangan menjalankan migration.
Jangan menyentuh database lama.

Tugas:

1. Deteksi OS/shell.
2. Deteksi folder project saat ini.
3. Deteksi path PHP.
4. Deteksi versi PHP.
5. Deteksi path Composer.
6. Deteksi versi Composer.
7. Deteksi path Node.js.
8. Deteksi versi Node.js.
9. Deteksi path npm.
10. Deteksi versi npm.
11. Deteksi path Git.
12. Deteksi versi Git.
13. Jika ada backend/artisan, deteksi path artisan dan Laravel version.
14. Jika ada backend/composer.json, tampilkan status composer backend.
15. Jika ada frontend/package.json, tampilkan npm scripts yang tersedia.
16. Jika ada frontend/vite.config.ts, tampilkan status Vite config.
17. Jika ada infrastructure/docker/docker-compose.yml, tampilkan status Docker Compose.
18. Jika ada backend/.env, tampilkan DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME tanpa menampilkan password.
19. Pastikan DB_DATABASE adalah internhub_rekrutmen_2026.
20. Beri tahu apakah environment siap untuk Laravel 13.
21. Jika belum siap, jelaskan apa yang kurang dan jangan perbaiki otomatis tanpa izin.

Untuk Linux/macOS/WSL/Git Bash gunakan:

pwd
which php || true
php -v || true
which composer || true
composer --version || true
which node || true
node -v || true
which npm || true
npm -v || true
which git || true
git --version || true
ls -la backend/artisan 2>/dev/null || true
cd backend 2>/dev/null && php artisan --version 2>/dev/null && cd ..
cat backend/composer.json 2>/dev/null || true
cat frontend/package.json 2>/dev/null || true
cat frontend/vite.config.ts 2>/dev/null || true
ls -la infrastructure/docker/docker-compose.yml 2>/dev/null || true
cat backend/.env 2>/dev/null | grep -E "DB_CONNECTION|DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME" || true

Untuk Windows PowerShell gunakan:

Get-Location
Get-Command php -ErrorAction SilentlyContinue
php -v
Get-Command composer -ErrorAction SilentlyContinue
composer --version
Get-Command node -ErrorAction SilentlyContinue
node -v
Get-Command npm -ErrorAction SilentlyContinue
npm -v
Get-Command git -ErrorAction SilentlyContinue
git --version
Get-ChildItem backend/artisan -ErrorAction SilentlyContinue
Set-Location backend; php artisan --version; Set-Location ..
Get-Content backend/composer.json -ErrorAction SilentlyContinue
Get-Content frontend/package.json -ErrorAction SilentlyContinue
Get-Content frontend/vite.config.ts -ErrorAction SilentlyContinue
Get-ChildItem infrastructure/docker/docker-compose.yml -ErrorAction SilentlyContinue
Get-Content backend/.env -ErrorAction SilentlyContinue | Select-String "DB_CONNECTION|DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME"

Format laporan:

Environment Detection:
- OS/shell:
- Root project:
- Folder aktif:
- PHP path:
- PHP version:
- Composer path:
- Composer version:
- Node path:
- Node version:
- npm path:
- npm version:
- Git path:
- Git version:
- backend/ tersedia:
- backend/artisan tersedia:
- backend/composer.json tersedia:
- Laravel version jika tersedia:
- frontend/ tersedia:
- frontend/package.json tersedia:
- frontend/vite.config.ts tersedia:
- infrastructure/ tersedia:
- infrastructure/docker/docker-compose.yml tersedia:
- docs/ tersedia:
- scripts/ tersedia:
- .agents/rules tersedia:
- DB connection:
- DB host:
- DB port:
- DB database:
- DB username:
- Apakah database baru sudah benar:
- Status struktur folder:
- Status kesiapan:
- Masalah ditemukan:
- Rekomendasi:
```

---

# 8. BATCH 0 — PRD & FOUNDATION

```text
Kerjakan Batch 0 - PRD & Foundation untuk project InternHub.

Pada Batch 0 Anda hanya boleh membuat dokumentasi.

Dilarang:

- composer install
- composer create-project
- laravel new
- npm install
- php artisan
- php artisan migrate
- php artisan migrate:fresh
- php artisan db:wipe
- drop database
- truncate table
- membuat migration
- membuat controller
- membuat model
- membuat Vue page
- coding fitur aplikasi

Buat file berikut:

1. PRD.md
2. Architecture.md
3. docs/database.md
4. docs/database-new-project.md
5. docs/project-structure.md
6. docs/roles-permissions.md
7. docs/security.md
8. docs/authentication-flow.md
9. docs/authorization-design.md
10. docs/ai-module.md
11. docs/activity-log.md
12. docs/audit-security-event.md
13. docs/realtime-notification.md
14. docs/realtime-location-attendance.md
15. docs/nearby-internship.md
16. docs/integration.md
17. docs/mcp.md
18. docs/skill-rules.md
19. docs/testing.md
20. docs/docker-plan.md
21. docs/email.md
22. docs/design-system.md
23. docs/anti-ai-looking-ui.md
24. docs/homepage-public-experience.md
25. docs/local-network-demo.md
26. docs/api.md
27. docs/openapi-plan.md
28. docs/openapi.yaml
29. docs/web-service-architecture.md
30. docs/health-check.md
31. docs/api-response-standard.md
32. docs/queue-scheduler.md
33. docs/adr/0001-use-laravel-13.md
34. docs/adr/0002-use-sanctum-session-auth.md
35. docs/adr/0003-use-postgresql-new-database.md
36. docs/adr/0004-use-redis-queue-cache.md
37. docs/adr/0005-use-inertia-vue-typescript.md
38. docs/adr/0006-use-policy-gate-authorization.md
39. docs/adr/0007-use-modular-integration-provider.md
40. docs/adr/0008-use-mcp-and-skill-rules.md
41. docs/adr/0009-use-ai-human-in-the-loop.md
42. docs/adr/0010-use-role-based-activity-log.md
43. docs/adr/0011-use-consent-based-location.md
44. docs/adr/0012-use-leaflet-openstreetmap-haversine-for-mvp.md
45. docs/adr/0013-use-resend-for-email-otp.md
46. docs/adr/0014-use-local-network-demo-mode.md
47. docs/adr/0015-use-trust-building-homepage-and-motion.md
48. docs/adr/0016-use-anti-ai-looking-ui-rules.md
49. docs/adr/0017-use-separated-backend-frontend-folder-structure.md
50. docs/adr/0018-use-hybrid-inertia-rest-api-architecture.md
51. docs/adr/0019-use-standard-api-response-request-id.md
52. docs/adr/0020-use-health-check-queue-scheduler.md
53. docs/batch-reports/batch-00-prd-foundation.md

PRD.md wajib menjelaskan:

1. Ringkasan Produk
2. Problem Statement
3. Target Pengguna
4. Tujuan Produk
5. Non-Goals
6. Role Pengguna
7. User Journey
8. Fitur Utama
9. Fitur Per Role
10. Functional Requirements
11. Non-Functional Requirements
12. Security Requirements
13. Authorization Matrix
14. Authentication Flow
15. Login dan daftar dengan Google
16. Email verification dengan OTP 6 digit via Resend
17. Forgot password dengan Resend secure reset link + OTP fallback
18. Database baru internhub_rekrutmen_2026
19. Project Structure Requirements
20. Full Web Service Requirements
21. API Requirements
22. API Authentication Strategy
23. API Authorization Strategy
24. API Response Standard
25. Error Response Standard
26. Health Check Requirements
27. Queue Worker Requirements
28. Scheduler Requirements
29. API Documentation Requirements
30. OpenAPI/Postman Collection Requirements
31. Service Layer Architecture
32. Mobile-ready API Future Plan
33. Data Model
34. Integration Requirements
35. Queue & Background Jobs
36. UI/UX Requirements
37. Anti AI-looking UI Requirements
38. Homepage Public Experience Requirements
39. AI Requirements
40. Activity Log Requirements
41. Realtime & Attendance Requirements
42. Nearby Internship Requirements
43. Analytics & Reporting
44. Audit Logging
45. Testing Requirements
46. Localhost/LAN/Tunnel Demo Requirements
47. Deployment Requirements
48. Acceptance Criteria
49. Risks
50. Open Questions

Architecture.md wajib menjelaskan:

- stack final
- monorepo structure
- backend folder architecture
- frontend folder architecture
- infrastructure folder architecture
- docs folder architecture
- scripts folder architecture
- backend architecture
- frontend architecture
- database architecture
- database baru internhub_rekrutmen_2026
- larangan memakai database lama
- hybrid Inertia + REST API architecture
- /api/v1 route architecture
- Web controller vs API controller
- API Resource response architecture
- RequestIdMiddleware
- ApiResponse helper
- Standard exception/error handler
- Health check architecture
- Queue worker architecture
- Scheduler architecture
- API documentation architecture
- Inertia architecture dengan backend/ dan frontend/ terpisah
- build output frontend ke backend/public/build
- Sanctum session auth architecture
- Google OAuth architecture
- Resend OTP email architecture
- authorization architecture
- company scoping
- AI provider architecture
- activity/audit/security logging architecture
- queue/Redis/Horizon architecture
- realtime notification architecture
- attendance/realtime location architecture
- nearby search architecture
- external integration architecture
- private file storage architecture
- Cloudflare R2 architecture
- local network demo architecture
- homepage public experience architecture
- anti AI-looking UI architecture
- deployment architecture

Setelah selesai:

1. Tampilkan daftar file yang dibuat.
2. Tampilkan ringkasan PRD.
3. Tampilkan keputusan teknis utama.
4. Tampilkan keputusan database baru.
5. Tampilkan keputusan struktur folder.
6. Tampilkan keputusan full web service.
7. Tampilkan risiko dan open questions.
8. Berhenti.
9. Jangan lanjut Batch 1.
10. Tunggu review saya.
```

## REVIEW BATCH 0

```text
Review hasil Batch 0.

Tolong tampilkan ringkasan sederhana untuk saya sebagai orang awam:

1. Apa isi PRD.md?
2. Apa isi Architecture.md?
3. Apa keputusan database utama?
4. Apakah database baru internhub_rekrutmen_2026 sudah masuk desain?
5. Apakah ada larangan memakai database lama?
6. Apakah struktur folder backend/frontend/infrastructure/docs/scripts sudah masuk desain?
7. Apakah docs/project-structure.md dibuat?
8. Apakah full web service sudah masuk desain?
9. Apakah REST API /api/v1 sudah masuk desain?
10. Apakah ApiResponse helper sudah direncanakan?
11. Apakah RequestIdMiddleware sudah direncanakan?
12. Apakah health check sudah direncanakan?
13. Apakah queue dan scheduler sudah direncanakan?
14. Apakah docs/api.md dan docs/openapi.yaml sudah direncanakan?
15. Apa role dan permission utama?
16. Apakah login dan daftar dengan Google sudah masuk desain?
17. Apakah Resend OTP sudah masuk desain email verification?
18. Apakah konfigurasi mail lama MAIL_MAILER=log sudah dihapus dari rencana?
19. Apakah homepage public experience sudah dirancang?
20. Apakah anti AI-looking UI sudah dirancang?
21. Apa risiko terbesar project ini?
22. Apa yang harus saya cek sebelum menyetujui Batch 0?
23. Apakah ada bagian yang masih kurang jelas?

Jangan lanjut Batch 1.
Jangan install package.
Jangan menjalankan command.
Jangan menyentuh database.
Tunggu saya review.
```

Approval:

```text
PRD disetujui, lanjut Batch 1.
```

---

# 9. BATCH 1 — PROJECT SETUP + FULL WEB SERVICE FOUNDATION

```text
Kerjakan Batch 1 - Project Setup + Full Web Service Foundation.

Sebelum menjalankan Batch 1, lakukan Environment Detection jika belum dilakukan.

Tujuan Batch 1:

- Membuat root folder internhub jika belum ada.
- Membuat Laravel 13 di folder backend/ jika belum ada.
- Membuat frontend workspace di folder frontend/.
- Setup Inertia.js + Vue 3 + TypeScript.
- Setup Tailwind CSS 4.
- Setup PostgreSQL config.
- Setup database baru internhub_rekrutmen_2026.
- Setup Redis config.
- Setup Sanctum config awal.
- Setup Socialite untuk Google OAuth.
- Setup Horizon config awal.
- Setup Reverb config awal.
- Setup Docker Compose awal di infrastructure/docker/.
- Setup base layout modern.
- Setup auth layout modern.
- Setup public homepage layout foundation.
- Setup reusable auth components.
- Setup anti AI-looking UI foundation.
- Setup Google OAuth configuration placeholder.
- Setup Resend configuration untuk email verification OTP, forgot password, reset password, dan notification email.
- Setup README instalasi awal.
- Setup docs/email.md.
- Setup docs/local-network-demo.md.
- Setup docs/project-structure.md.
- Setup .env.example lengkap tanpa MAIL_MAILER=log.
- Setup .env.example dengan database baru internhub_rekrutmen_2026.
- Setup Cloudflare R2 mapping.
- Setup full web service foundation.

Full web service foundation yang wajib dibuat pada Batch 1:

Backend:
- Buat struktur backend/app/Http/Controllers/Api
- Buat struktur backend/app/Http/Controllers/Web
- Buat struktur backend/app/Http/Resources
- Buat struktur backend/app/Actions
- Buat struktur backend/app/Services
- Buat struktur backend/app/DTOs
- Buat struktur backend/app/QueryFilters
- Buat ApiResponse helper
- Buat RequestIdMiddleware
- Buat standard API success response
- Buat standard API error response skeleton
- Buat base API route /api/v1/health
- Buat base API route /api/v1/version
- Buat base API route /api/v1/status
- Buat docs/api.md
- Buat docs/openapi.yaml skeleton
- Buat docs/web-service-architecture.md
- Buat docs/api-response-standard.md
- Buat docs/health-check.md

Route:
- routes/api.php wajib memakai prefix /api/v1
- routes/web.php tetap untuk Inertia pages

Testing:
- Test health check endpoint
- Test API response format
- Test request_id muncul di error response skeleton jika memungkinkan

Sebelum menjalankan command:

1. Pastikan environment detection sudah dilakukan.
2. Tampilkan path PHP, Composer, Node.js, npm, Git, dan artisan jika ada.
3. Tampilkan folder aktif.
4. Tampilkan struktur folder saat ini.
5. Tampilkan database aktif jika backend/.env tersedia.
6. Tampilkan command yang akan dijalankan.
7. Jelaskan fungsi command dengan bahasa sederhana.
8. Jangan menjalankan command destruktif.
9. Jangan menghapus file tanpa izin.
10. Jangan membuat fitur auth penuh dulu selain setup package/config dasar dan layout foundation.
11. Jangan membuat dashboard role dulu.
12. Jangan menjalankan migration ke database lama.
13. Jangan menjalankan migrate:fresh tanpa approval.

Jika project Laravel belum ada, buat di folder backend/.

Command install Laravel yang benar dari root internhub/:

composer create-project laravel/laravel backend

Setelah Laravel dibuat, semua command artisan/composer harus dilakukan dari backend/.

Command backend benar:

cd backend
php artisan --version

Command frontend benar:

cd frontend
npm install
npm run dev

Command docker benar:

cd infrastructure/docker
docker compose up -d

Package backend yang boleh dipasang pada Batch 1:

- laravel/sanctum
- laravel/socialite
- laravel/horizon
- laravel/reverb
- predis/predis
- league/flysystem-aws-s3-v3
- spatie/laravel-permission
- resend/resend-php
- pestphp/pest
- pestphp/pest-plugin-laravel
- laravel/pint
- nunomaduro/larastan

Package frontend yang boleh dipasang pada Batch 1:

- @inertiajs/vue3
- vue
- typescript
- tailwindcss
- @tailwindcss/vite
- pinia
- @vueuse/core
- lucide-vue-next
- @headlessui/vue
- laravel-echo
- pusher-js
- leaflet
- @types/leaflet
- vue-tsc
- eslint
- prettier

Auth UI foundation minimal:

- AuthLayout
- AuthCard
- GoogleAuthButton
- AuthDivider dengan teks Atau
- PasswordField dengan show/hide icon
- PasswordStrengthMeter
- AuthLink
- FormError
- LoadingButton

Public homepage UI foundation minimal:

- PublicLayout
- HeroSection
- HeroMedia
- TrustBadge
- TrustSignalCard
- SectionHeader
- MarketingCard
- FeaturedInternshipCard
- HowItWorksStep
- FAQAccordion
- CTABanner
- AnimatedSection
- MotionButton

.env.example wajib berada di backend/.env.example dan berisi:

- APP_NAME=InternHub
- DB PostgreSQL dengan database baru internhub_rekrutmen_2026
- Redis
- Sanctum stateful domains
- Google OAuth
- Resend
- Mailpit fallback optional
- Reverb
- Horizon
- R2
- AI provider
- Nearby config
- Realtime location config
- Localhost Mode
- LAN/WiFi Mode
- Vite host config

Gunakan database config:

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=internhub_rekrutmen_2026
DB_USERNAME=internhub_user
DB_PASSWORD=internhub_password

Hapus konfigurasi mail lama:

- MAIL_MAILER=log
- MAIL_SCHEME=null
- MAIL_HOST=127.0.0.1
- MAIL_PORT=2525
- MAIL_USERNAME=null
- MAIL_PASSWORD=null
- MAIL_FROM_ADDRESS="hello@example.com"
- MAIL_FROM_NAME="${APP_NAME}"

Output Batch 1 wajib:

1. Struktur folder root berhasil dibuat.
2. backend/ berisi Laravel.
3. backend/artisan tersedia.
4. backend/composer.json tersedia.
5. frontend/ tersedia.
6. frontend/package.json tersedia.
7. frontend/resources/js tersedia.
8. frontend/resources/css tersedia.
9. frontend/vite.config.ts tersedia.
10. infrastructure/ tersedia.
11. infrastructure/docker/docker-compose.yml tersedia jika Docker disiapkan.
12. docs/ tersedia.
13. scripts/ tersedia.
14. Project Laravel siap.
15. Package backend terinstall.
16. Package frontend terinstall.
17. Inertia Vue TypeScript aktif.
18. Tailwind aktif.
19. Docker Compose plan atau file awal tersedia.
20. Database baru internhub_rekrutmen_2026 masuk backend/.env.example.
21. .env.example lengkap termasuk Resend.
22. Socialite config placeholder siap.
23. Auth layout foundation tersedia.
24. Public homepage layout foundation tersedia.
25. Anti AI-looking UI foundation tersedia.
26. Cloudflare R2 mapping tersedia.
27. Full web service folder foundation tersedia.
28. ApiResponse helper tersedia.
29. RequestIdMiddleware tersedia.
30. Route /api/v1/health tersedia.
31. Route /api/v1/version tersedia.
32. docs/api.md tersedia.
33. docs/openapi.yaml skeleton tersedia.
34. README update.
35. docs/email.md update.
36. docs/local-network-demo.md update.
37. docs/project-structure.md update.
38. docs/batch-reports/batch-01-project-setup.md dibuat.

Setelah selesai:

1. Tampilkan file dibuat/diubah berdasarkan kategori:
   - backend
   - frontend
   - infrastructure
   - docs
   - scripts
2. Tampilkan command yang dijalankan dan dari folder mana.
3. Tampilkan path PHP, Composer, Node.js, npm, Git, artisan.
4. Tampilkan database aktif.
5. Tampilkan status database baru.
6. Tampilkan status Socialite.
7. Tampilkan status Resend config.
8. Tampilkan status Cloudflare R2 config.
9. Tampilkan status auth UI foundation.
10. Tampilkan status public homepage foundation.
11. Tampilkan status full web service foundation.
12. Tampilkan status API /api/v1/health.
13. Tampilkan status struktur folder.
14. Tampilkan error jika ada.
15. Tampilkan cara menjalankan project.
16. Berhenti dan tunggu review saya.
```

## REVIEW BATCH 1

```text
Review Batch 1 untuk saya sebagai orang awam.

Tolong cek:

1. Apakah Laravel sudah benar terpasang di backend/?
2. Apakah backend/artisan tersedia?
3. Apakah backend/composer.json tersedia?
4. Apakah Inertia + Vue 3 + TypeScript sudah siap?
5. Apakah frontend/package.json tersedia?
6. Apakah frontend/resources/js tersedia?
7. Apakah frontend/resources/css tersedia?
8. Apakah frontend/vite.config.ts tersedia?
9. Apakah Tailwind CSS sudah siap?
10. Apakah npm path terbaca?
11. Apakah Composer path terbaca?
12. Apakah artisan bisa dijalankan dari backend/?
13. Apakah .env.example sudah lengkap?
14. Apakah database baru internhub_rekrutmen_2026 sudah dipakai?
15. Apakah tidak memakai database lama?
16. Apakah infrastructure/ tersedia?
17. Apakah infrastructure/docker/docker-compose.yml tersedia jika Docker disiapkan?
18. Apakah docs/ tersedia?
19. Apakah scripts/ tersedia?
20. Apakah docs/project-structure.md dibuat?
21. Apakah README menjelaskan cara mencari backend dan frontend?
22. Apakah command artisan hanya dijalankan dari backend/?
23. Apakah command npm hanya dijalankan dari frontend/?
24. Apakah Docker command dijelaskan dari infrastructure/docker/?
25. Apakah Docker Compose sudah masuk akal?
26. Apakah Socialite sudah siap?
27. Apakah konfigurasi Google login placeholder sudah benar?
28. Apakah konfigurasi Resend sudah masuk ke backend/.env.example?
29. Apakah package resend/resend-php sudah dipasang?
30. Apakah konfigurasi MAIL_MAILER=log sudah dihapus?
31. Apakah auth layout modern sudah disiapkan?
32. Apakah tombol Google auth reusable sudah disiapkan?
33. Apakah PasswordField dengan show/hide icon sudah disiapkan?
34. Apakah password strength meter sudah disiapkan?
35. Apakah public homepage foundation sudah dibuat?
36. Apakah anti AI-looking UI foundation sudah dibuat?
37. Apakah full web service foundation sudah dibuat?
38. Apakah folder Controllers/Api tersedia?
39. Apakah folder Http/Resources tersedia?
40. Apakah folder Actions, Services, DTOs, QueryFilters tersedia?
41. Apakah ApiResponse helper tersedia?
42. Apakah RequestIdMiddleware tersedia?
43. Apakah /api/v1/health tersedia?
44. Apakah /api/v1/version tersedia?
45. Apakah docs/api.md tersedia?
46. Apakah docs/openapi.yaml tersedia?
47. Apakah test API response format tersedia?
48. Apakah struktur folder ini mudah dipahami user awam?
49. Apakah ada error setup?
50. Apa yang harus saya lihat untuk memastikan Batch 1 aman?

Tampilkan:

- Ringkasan hasil Batch 1
- File penting yang dibuat/diubah berdasarkan folder
- Command yang dijalankan dan dari folder mana
- Path PHP, Composer, Node.js, npm, Git, artisan
- Database aktif
- Status database baru
- Error jika ada
- Status setup Google auth
- Status setup Resend
- Status auth UI foundation
- Status homepage foundation
- Status full web service foundation
- Status API health check
- Status struktur folder
- Apakah aman lanjut Batch 2 atau perlu diperbaiki dulu

Jangan lanjut Batch 2 sebelum saya setujui.
```

Approval:

```text
Batch 1 disetujui, lanjut Batch 2.
```

---

# 10. BATCH 2 — AUTH, SECURITY & AUTH API FOUNDATION

```text
Kerjakan Batch 2 - Auth, Security & Auth API Foundation.

Tujuan Batch 2:

- Register dengan email + password.
- Register dengan Google.
- Login email/password.
- Login dengan Google.
- Logout aman.
- Google OAuth via Socialite.
- Session rotation setelah login.
- CAPTCHA validation structure.
- Login rate limiter.
- Login attempt logging.
- Suspicious login baseline.
- Forgot password menggunakan Resend.
- Reset password menggunakan Resend.
- Email verification menggunakan OTP 6 digit via Resend.
- Device/session management.
- Auth API endpoints dengan prefix /api/v1/auth.

Auth API minimal:

- POST /api/v1/auth/login
- POST /api/v1/auth/register
- POST /api/v1/auth/logout
- POST /api/v1/auth/forgot-password
- POST /api/v1/auth/reset-password
- POST /api/v1/auth/email/verify-otp
- POST /api/v1/auth/email/resend-otp
- GET /api/v1/auth/me

Sebelum migration atau database command:

1. Pastikan berada di folder backend/.
2. Tampilkan DB_DATABASE aktif.
3. Pastikan DB_DATABASE adalah internhub_rekrutmen_2026.
4. Jangan lanjut jika database aktif bukan internhub_rekrutmen_2026.
5. Jangan menjalankan migrate:fresh tanpa approval.

Wajib ikuti flow backend:

Route -> Controller -> Form Request -> Policy/Gate jika relevan -> Action -> Service -> Model -> Event/Listener -> Notification/Mail -> Activity Log -> Audit Log/Security Event jika relevan.

Dilarang:

- Business logic besar di controller.
- Auth check hanya di frontend.
- Menyimpan password/token plaintext.
- Menyimpan OTP plaintext.
- Membuat Google OAuth tanpa state validation.
- Mengabaikan rate limit.
- Expose RESEND_API_KEY ke frontend.
- Log RESEND_API_KEY.
- Log OTP plaintext.
- Menggunakan MAIL_MAILER=log sebagai default utama.
- Menjalankan migration ke database lama.
- Menaruh Controller di frontend/.
- Menaruh Vue page di backend/app.

Backend minimal:

- Web Auth controllers tipis.
- API Auth controllers tipis.
- GoogleAuthController atau Auth/SocialiteController.
- Form Request untuk login/register/verify OTP/resend OTP/forgot password/reset password.
- Auth actions.
- Google auth actions.
- AuthService.
- OtpService.
- EmailVerificationOtpService.
- GenerateEmailVerificationOtpAction.
- SendEmailVerificationOtpAction.
- VerifyEmailOtpAction.
- ResendEmailVerificationOtpAction.
- PasswordResetOtpService jika mode OTP reset digunakan.
- SendPasswordResetLinkAction.
- SendPasswordResetOtpAction jika mode OTP reset digunakan.
- VerifyPasswordResetOtpAction jika mode OTP reset digunakan.
- Notification/Mail class untuk email OTP verification via Resend.
- Notification/Mail class untuk forgot password via Resend.
- LoginAttemptLogger service.
- SuspiciousLoginService.
- UserSessionService.
- SecurityEventLogger skeleton jika belum ada.
- ActivityLogger skeleton jika belum ada.
- Migration auth/security yang dibutuhkan.
- social_accounts table.
- email_verification_otps table.
- password_reset_otps table optional.
- Model relation dasar.
- API Resource untuk User/Auth response.

Frontend minimal:

- Login page premium.
- Register page premium.
- Forgot password page.
- Reset password page.
- Email verification OTP page.
- Google login button.
- Google register button.
- Form error state.
- Loading state.
- Password visibility toggle.
- Password strength meter.

Testing:

- Register web test.
- Login web test.
- Logout web test.
- Register API test.
- Login API test.
- Logout API test.
- Auth me API test.
- API response format test.
- Google login redirect test.
- Google callback mocked test.
- Login rate limit test.
- Login attempt logging test.
- Forgot password test.
- Reset password test.
- Email verification OTP via Resend notification test menggunakan Mail/Notification fake.
- Resend OTP cooldown test.
- CAPTCHA validation test.

Setelah selesai:

1. Jalankan test yang relevan jika environment siap.
2. Buat docs/batch-reports/batch-02-auth-security-api.md.
3. Update docs/api.md.
4. Update docs/openapi.yaml.
5. Tampilkan file dibuat/diubah berdasarkan folder.
6. Tampilkan command dijalankan dan dari folder mana.
7. Tampilkan database aktif.
8. Tampilkan flow login/register.
9. Tampilkan flow Google auth.
10. Tampilkan flow email verification OTP Resend.
11. Tampilkan daftar Auth API endpoint.
12. Tampilkan risiko.
13. Berhenti dan tunggu review saya.
```

## REVIEW BATCH 2

```text
Review Batch 2 - Auth, Security & Auth API Foundation untuk saya sebagai orang awam.

Tolong cek:

1. Apakah database aktif tetap internhub_rekrutmen_2026?
2. Apakah command backend dijalankan dari backend/?
3. Apakah command frontend dijalankan dari frontend/?
4. Apakah register email/password sudah berjalan?
5. Apakah register dengan Google sudah berjalan?
6. Apakah login email/password sudah berjalan?
7. Apakah login dengan Google sudah berjalan?
8. Apakah logout aman sudah berjalan?
9. Apakah Auth API /api/v1/auth/login tersedia?
10. Apakah Auth API /api/v1/auth/register tersedia?
11. Apakah Auth API /api/v1/auth/logout tersedia?
12. Apakah Auth API /api/v1/auth/me tersedia?
13. Apakah Auth API response memakai format standar?
14. Apakah Auth API error response memakai request_id?
15. Apakah session rotation setelah login sudah dibuat?
16. Apakah Google login/register sudah disiapkan dengan benar?
17. Apakah register email/password otomatis mengirim OTP ke email terdaftar via Resend?
18. Apakah OTP 6 digit dikirim seperti template email modern?
19. Apakah halaman input OTP verification dibuat?
20. Apakah OTP tidak disimpan plaintext?
21. Apakah OTP punya expiry 10 menit?
22. Apakah OTP punya attempt limit?
23. Apakah resend OTP punya cooldown?
24. Apakah user belum verified tidak bisa masuk dashboard penuh?
25. Apakah Google register tidak perlu OTP jika Google email_verified = true?
26. Apakah forgot password mengirim reset link via Resend?
27. Apakah forgot password menyediakan OTP reset fallback jika mode OTP aktif?
28. Apakah reset password token/OTP expired dan aman?
29. Apakah CAPTCHA validation structure sudah ada?
30. Apakah login rate limiter sudah aktif?
31. Apakah login attempt logging sudah mencatat success dan failed login?
32. Apakah suspicious login baseline sudah dibuat?
33. Apakah device/session management sudah dibuat?
34. Apakah controller tetap tipis?
35. Apakah business logic masuk ke Action/Service?
36. Apakah Form Request dipakai untuk request auth?
37. Apakah audit log/security event dibuat untuk login penting?
38. Apakah ada password/token/secret/OTP yang tercatat di log?
39. Apakah RESEND_API_KEY hanya ada di backend .env?
40. Apakah tombol Google auth muncul di UI login?
41. Apakah tombol Google auth muncul di UI register?
42. Apakah form login dan register sudah modern seperti referensi?
43. Apakah email field menolak nomor HP seperti 81188878595?
44. Apakah test auth web dan API sudah dibuat dan dijalankan?

Tampilkan:

- Ringkasan hasil Batch 2
- File backend yang dibuat/diubah
- File frontend yang dibuat/diubah
- File docs yang dibuat/diubah
- Command yang dijalankan dan dari folder mana
- Database aktif
- Test yang berhasil/gagal
- Status Google auth
- Status Resend email verification OTP
- Status UI login/register/OTP
- Status Auth API
- Masalah atau risiko
- Apakah aman lanjut Batch 3 atau perlu diperbaiki dulu

Jangan lanjut Batch 3 sebelum saya setujui.
```

Approval:

```text
Batch 2 disetujui, lanjut Batch 3.
```

---

# 11. TEMPLATE UNTUK BATCH 3 SAMPAI BATCH 25

```text
Kerjakan Batch [NAMA BATCH].

Sebelum mulai:

1. Baca .agents/rules.
2. Baca batch report sebelumnya.
3. Jangan lompat scope batch.
4. Jangan membuat fitur di luar batch kecuali dependency kecil yang wajib.
5. Jika perlu command, tampilkan command dan jelaskan fungsinya dulu.
6. Jika menyentuh backend, pastikan berada di backend/.
7. Jika menyentuh frontend, pastikan berada di frontend/.
8. Jika menyentuh Docker, pastikan berada di infrastructure/docker/.
9. Jika menyentuh database, tampilkan DB_DATABASE aktif.
10. Pastikan DB_DATABASE adalah internhub_rekrutmen_2026.
11. Jangan menjalankan command destruktif tanpa approval.

Wajib ikuti arsitektur:

Route -> Controller -> Form Request -> Policy/Gate -> Action/Service -> DTO -> Model -> Event/Job/Notification -> Activity/Audit/Security Log jika relevan.

Jika batch membuat fitur utama, buat juga endpoint API /api/v1 jika relevan.

Output wajib:

1. File backend yang dibuat/diubah.
2. File frontend yang dibuat/diubah.
3. File infrastructure yang dibuat/diubah.
4. File docs yang dibuat/diubah.
5. Script yang dibuat/diubah.
6. Command dijalankan dan dari folder mana.
7. Database aktif jika menyentuh database.
8. API endpoint yang dibuat/diubah jika ada.
9. API docs yang diupdate jika ada.
10. Test yang dibuat.
11. Test yang dijalankan.
12. Risiko.
13. Batch report di docs/batch-reports/.
14. Berhenti dan tunggu review saya.
```

---

# 12. BATCH 3 SAMPAI BATCH 25

## BATCH 3 — ROLE, PERMISSION, POLICY, GATE

```text
Kerjakan Batch 3 - Role, Permission, Policy, Gate.

Tujuan:

- Membuat role global.
- Membuat company role.
- Membuat permission enum.
- Setup Spatie Permission jika belum.
- Membuat Gate utama.
- Membuat Policy utama.
- Membuat Role middleware.
- Membuat CompanyScope middleware.
- Membuat RoleResolver service.
- Membuat currentCompany helper/resolver.
- Membuat authorization tests.
- Membuat authorization response API standar untuk endpoint protected.

Aturan penting:

1. Publik/Tamu bukan role database.
2. Role database hanya USER, HR, MENTOR, ADMIN, SUPER_ADMIN.
3. Company role terpisah: owner, hr, mentor, viewer.
4. Jangan hardcode role string berulang.
5. Semua role/permission harus melalui enum atau constant.
6. Frontend boleh hide menu, tapi backend tetap wajib Policy/Gate.
7. HR tidak boleh melihat company lain.
8. Mentor tidak boleh melihat mentee yang bukan assignment-nya.
9. Database aktif harus internhub_rekrutmen_2026.
10. File backend masuk backend/.
11. File frontend masuk frontend/.
12. API protected endpoint harus mengembalikan error standar jika unauthorized.

Buat minimal:

- app/Enums/UserRole.php
- app/Enums/CompanyRole.php
- app/Enums/Permission.php
- app/Services/Auth/RoleResolver.php
- middleware role
- middleware company scope
- Gate definitions
- Policy skeleton untuk modul utama
- Seeder role dan permission
- Tests authorization dasar
- Tests unauthorized API response format
- docs/batch-reports/batch-03-role-permission-policy-gate.md

Setelah selesai, berhenti dan tunggu review saya.
```

## BATCH 4 — WORLD-CLASS UI/UX DESIGN SYSTEM FOUNDATION

```text
Kerjakan Batch 4 - World-Class UI/UX Design System Foundation.

Tujuan:
Membuat design system kelas dunia untuk InternHub sebelum halaman besar dibuat.

Standar desain:

- world-class
- premium
- sophisticated
- highly-polished
- modern 2026
- enterprise-grade
- youth-friendly
- accessible
- responsive
- tidak seperti template admin lama
- tidak terlihat AI-generated
- human-made
- natural
- profesional
- trust-building

Wajib buat:

1. Design tokens.
2. Semantic color tokens.
3. Typography scale.
4. Spacing scale.
5. Radius scale.
6. Shadow/elevation scale.
7. Focus state.
8. Motion guideline.
9. Anti AI-looking UI guideline.
10. Base app layout.
11. Public layout.
12. Auth layout.
13. Dashboard shell.
14. Sidebar modern.
15. Topbar modern.
16. Breadcrumb.
17. Button component.
18. Input component.
19. Select component.
20. Textarea component.
21. Checkbox component.
22. Switch component.
23. Badge component.
24. StatusBadge component.
25. RoleBadge component.
26. Card component.
27. Modal component.
28. Drawer component.
29. Toast pattern.
30. Alert component.
31. EmptyState component.
32. LoadingSkeleton component.
33. DataTable foundation.
34. Pagination foundation.
35. FileUpload foundation.
36. AuthCard.
37. GoogleAuthButton.
38. AuthDivider.
39. PasswordField.
40. PasswordStrengthMeter.
41. Hero section pattern.
42. Trust badge pattern.
43. FAQ accordion premium.
44. CTA banner premium.
45. Public section container system.
46. Motion guideline / animation tokens.
47. Hover interaction rules.
48. Scroll reveal rules.
49. Homepage hero media rules.
50. API loading/error state pattern untuk frontend service calls.

Buat juga:

- docs/design-system.md
- docs/anti-ai-looking-ui.md
- docs/batch-reports/batch-04-uiux-design-system.md

Setelah selesai, berhenti dan tunggu review saya.
```

## BATCH 5 — PUBLIC WEBSITE, PUBLIC API & SEARCH

```text
Kerjakan Batch 5 - Public Website, Public API & Search.

Buat:

- Homepage premium yang terlihat seperti website rekrutmen magang sungguhan.
- Hero section dengan foto/visual bertema magang.
- Copywriting natural, tidak AI banget.
- Trust section.
- Featured internship section.
- How It Works section.
- Why InternHub section.
- Nearby internship teaser.
- FAQ section.
- Final CTA section.
- Internship list public.
- Internship detail public.
- Company public profile.
- Search/filter internship.
- PostgreSQL full-text search layer.
- Public controllers tipis.
- Public API controllers tipis.
- Search service.
- Search DTO/filter.
- Published internship query only.
- Pagination.
- Empty/loading/error state.
- Motion/micro-interaction halus.
- docs/batch-reports/batch-05-public-website-search.md.

Public API minimal:

- GET /api/v1/public/internships
- GET /api/v1/public/internships/{slug}
- GET /api/v1/public/companies/{slug}
- GET /api/v1/public/search/internships

Security:

- Publik hanya boleh melihat lowongan published.
- Jangan expose data private.
- Jangan memakai testimoni palsu.
- Jangan memakai angka statistik palsu.
- Jangan memakai logo perusahaan palsu.

Testing:

- Public can view published internship via web.
- Public can view published internship via API.
- Public cannot view draft/private internship via web.
- Public cannot view draft/private internship via API.
- Search filter works.
- Homepage public renders correctly.
- API response format consistent.

Update docs/api.md dan docs/openapi.yaml.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 6 — USER DASHBOARD, CANDIDATE API & APPLICATION SERVICE

```text
Kerjakan Batch 6 - User Dashboard, Candidate API & Application Service.

Buat:

- User dashboard premium.
- Profile completion.
- Biodata, education, skill.
- CV upload.
- Portfolio upload.
- Saved internships.
- Apply internship.
- Application tracking.
- File upload component.
- Application timeline.
- Status badge.
- ApplicationService.
- DocumentService.
- Candidate API endpoints.
- docs/batch-reports/batch-06-user-dashboard.md.

Candidate API minimal:

- GET /api/v1/candidate/dashboard
- GET /api/v1/candidate/profile
- PUT /api/v1/candidate/profile
- POST /api/v1/candidate/documents
- GET /api/v1/candidate/documents
- DELETE /api/v1/candidate/documents/{id}
- POST /api/v1/candidate/internships/{id}/apply
- GET /api/v1/candidate/applications
- GET /api/v1/candidate/applications/{id}
- POST /api/v1/candidate/saved-internships/{id}
- DELETE /api/v1/candidate/saved-internships/{id}

Security:

- User hanya melihat data sendiri.
- File kandidat private.
- Upload validasi MIME, extension, size.
- Document access log disiapkan.
- Apply internship memakai transaction.
- Kandidat tidak boleh apply lowongan yang sama dua kali.
- Kandidat tidak boleh apply lowongan closed/expired.
- Activity log dan audit log untuk aksi penting.

Testing:

- User can update own profile via web.
- User can update own profile via API.
- User cannot update other profile.
- User can apply internship.
- User cannot apply twice.
- File access policy.
- Candidate API response format.

Update docs/api.md dan docs/openapi.yaml.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 7 — COMPANY, HR DASHBOARD & HR API

```text
Kerjakan Batch 7 - Company, HR Dashboard & HR API.

Buat:

- Company management.
- Company membership.
- Company switcher.
- HR dashboard premium.
- Internship CRUD.
- Applicant pipeline awal.
- Application review.
- Interview schedule.
- Accept/reject candidate.
- Assign mentor.
- InternshipService.
- HRApplicationService.
- InterviewService.
- HR API endpoints.
- docs/batch-reports/batch-07-company-hr-dashboard.md.

HR API minimal:

- GET /api/v1/hr/dashboard
- GET /api/v1/hr/internships
- POST /api/v1/hr/internships
- PUT /api/v1/hr/internships/{id}
- DELETE /api/v1/hr/internships/{id}
- GET /api/v1/hr/applications
- GET /api/v1/hr/applications/{id}
- PATCH /api/v1/hr/applications/{id}/status
- POST /api/v1/hr/applications/{id}/notes
- POST /api/v1/hr/applications/{id}/interviews
- GET /api/v1/hr/reports/recruitment

Security:

- HR hanya mengakses company scope authorized.
- HR global hanya jika diberi permission Admin/Super Admin.
- HR tidak boleh melihat kandidat company lain.
- Accept/reject harus human decision.
- Semua perubahan status lamaran wajib audit log.

Testing:

- HR cannot access other company via web.
- HR cannot access other company via API.
- HR can create internship for own company.
- HR can review application in scope.
- HR cannot review out-of-scope application.
- HR API response format.

Update docs/api.md dan docs/openapi.yaml.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 8 — MENTOR DASHBOARD & MENTOR API

```text
Kerjakan Batch 8 - Mentor Dashboard & Mentor API.

Buat:

- Mentor dashboard premium.
- Assigned interns.
- Mentee detail.
- Task management.
- Feedback.
- Mentoring session.
- Evaluation.
- Timeline perkembangan.
- MentorService.
- Mentor API endpoints.
- docs/batch-reports/batch-08-mentor-dashboard.md.

Mentor API minimal:

- GET /api/v1/mentor/dashboard
- GET /api/v1/mentor/mentees
- GET /api/v1/mentor/mentees/{id}
- POST /api/v1/mentor/tasks
- POST /api/v1/mentor/feedback
- POST /api/v1/mentor/evaluations

Security:

- Mentor hanya melihat peserta yang ditugaskan.
- Mentor tidak boleh melihat semua kandidat.
- Mentor tidak boleh mengubah keputusan HR tanpa permission.
- Feedback dan evaluasi dicatat activity log.

Testing:

- Mentor can view assigned mentee via web/API.
- Mentor cannot view unassigned mentee via web/API.
- Mentor can create task for assigned mentee.
- Mentor can submit feedback.
- Mentor API response format.

Update docs/api.md dan docs/openapi.yaml.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 9 — ADMIN DASHBOARD & ADMIN API

```text
Kerjakan Batch 9 - Admin Dashboard & Admin API.

Buat:

- Admin dashboard premium.
- User moderation terbatas.
- Company moderation.
- Internship moderation.
- Master data management.
- Report viewer.
- Location moderation.
- Admin API endpoints.
- docs/batch-reports/batch-09-admin-dashboard.md.

Admin API minimal:

- GET /api/v1/admin/dashboard
- GET /api/v1/admin/users
- PATCH /api/v1/admin/users/{id}/role
- GET /api/v1/admin/companies
- GET /api/v1/admin/audit-logs
- GET /api/v1/admin/security-events
- GET /api/v1/admin/system/health

Security:

- Admin tidak boleh menghapus Super Admin.
- Admin tidak boleh akses secret integration.
- Admin tidak boleh ubah system critical settings.
- Admin hanya melihat security event terbatas sesuai permission.
- Semua moderation action wajib audit log.

Testing:

- Admin moderation allowed via web/API.
- Admin cannot access super admin settings.
- Moderation creates audit log.
- Admin API response format.

Update docs/api.md dan docs/openapi.yaml.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 10 — SUPER ADMIN DASHBOARD

```text
Kerjakan Batch 10 - Super Admin Dashboard.

Buat:

- Super Admin dashboard enterprise-grade.
- Global user management.
- Role/permission management.
- Audit log viewer.
- Security event viewer.
- Integration management.
- System settings.
- Feature flags.
- Horizon access guard.
- Super Admin API jika relevan.
- docs/batch-reports/batch-10-super-admin-dashboard.md.

Security:

- Super Admin tetap tidak boleh melihat secret/token/password plaintext.
- Semua akses data sensitif harus audit log.
- Role/permission changes harus security event.
- Feature flag/system setting changes harus audit log.

Testing:

- Super Admin access.
- Non-super-admin denied.
- Role change audit/security event.
- Feature flag change audit log.
- API response format jika endpoint dibuat.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 11 — EXTERNAL INTEGRATION SERVICE

```text
Kerjakan Batch 11 - External Integration Service.

Buat:

- MagangHub official provider placeholder.
- CSV import provider.
- Manual feed provider.
- Partner webhook provider.
- Sync job.
- IntegrationService.
- Integration log.
- Duplicate detection.
- Admin approval before publish.
- Integration API untuk internal admin jika relevan.
- docs/batch-reports/batch-11-external-integration.md.

Aturan:

- Jangan scraping ilegal MagangHub.
- Jangan bergantung pada unofficial scraper API.
- Semua data eksternal diberi source label.
- Credential encrypted.
- Semua sync punya log.
- Import pending review dulu.

Testing:

- CSV import creates pending external listings.
- Duplicate detection works.
- Credentials not exposed plaintext.
- Admin approval publishes listing.
- Sync job test.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 12 — QUEUE, NOTIFICATION, HORIZON, REVERB

```text
Kerjakan Batch 12 - Queue, Notification, Horizon, Reverb.

Buat:

- Notification system.
- NotificationService.
- Email queue menggunakan Resend untuk email notification.
- Realtime notification.
- Private channel authorization.
- Presence channel jika dibutuhkan.
- Horizon dashboard authorization.
- Notification drawer frontend.
- Realtime toast.
- Notification API endpoints.
- Queue worker documentation.
- docs/batch-reports/batch-12-queue-notification-horizon-reverb.md.

Notification API minimal:

- GET /api/v1/notifications
- PATCH /api/v1/notifications/{id}/read
- PATCH /api/v1/notifications/read-all

Security:

- Semua broadcasting channel wajib authorization callback.
- User hanya menerima notifikasi miliknya.
- HR hanya menerima company notification sesuai scope.
- Mentor hanya menerima notification assignment-nya.
- Horizon hanya Admin/Super Admin.
- RESEND_API_KEY tidak boleh muncul di frontend/log.

Testing:

- Notification dispatch.
- Email notification via Resend dengan Mail/Notification fake.
- Private channel authorization.
- Horizon access denied for non-admin.
- Notification API response format.

Update docs/api.md, docs/openapi.yaml, docs/queue-scheduler.md.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 13 — AI FOUNDATION

```text
Kerjakan Batch 13 - AI Foundation.

Buat:

- AI config.
- AiProvider interface.
- FakeAiProvider.
- LocalLlmProvider.
- GeminiProvider.
- AiService.
- AI DTO.
- AI enum.
- AI policy.
- AI rate limit.
- AI safety guard.
- AI usage log.
- AI prompt template structure.
- AI panel foundation.
- AI suggestion card foundation.
- AI API foundation jika relevan.
- docs/batch-reports/batch-13-ai-foundation.md.

Prinsip:

AI assists, human decides.
AI tidak boleh mengambil keputusan final.
AI tidak boleh bypass Policy/Gate.
AI tidak boleh expose secret/token/password/API key.
AI UI tidak boleh membuat website terlihat AI banget.

Testing:

- FakeAiProvider test.
- AI authorization test.
- AI rate limit test.
- AI safety guard test.
- AI usage log test.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 14 — AI PUBLIC & USER

```text
Kerjakan Batch 14 - AI Public & User.

Buat:

- Public AI internship finder.
- Public AI FAQ.
- User profile review assistant.
- User CV summary helper.
- User internship recommendation.
- User application letter draft.
- User interview preparation.
- AI frontend widgets.
- AI API endpoints untuk public/user jika relevan.
- docs/batch-reports/batch-14-ai-public-user.md.

Security:

- Public AI rate limited.
- User AI hanya data user sendiri.
- CV summary tidak dikirim ke external provider tanpa consent.
- AI output penting human_review_required true.
- AI tidak membuat pengalaman palsu.

Testing:

- Public AI rate limit.
- User cannot access other user AI context.
- CV summary requires consent if external provider.
- AI usage log created.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 15 — AI HR & MENTOR

```text
Kerjakan Batch 15 - AI HR & Mentor.

Buat:

- HR job description generator.
- HR candidate summary.
- HR candidate screening.
- HR interview question generator.
- HR pipeline insight.
- Mentor task generator.
- Mentor feedback draft.
- Mentor evaluation summary.
- AI API endpoints untuk HR/Mentor jika relevan.
- docs/batch-reports/batch-15-ai-hr-mentor.md.

Security dan fairness:

- AI tidak boleh auto-reject kandidat.
- AI tidak boleh auto-accept kandidat.
- Candidate screening human_review_required true.
- Screening tidak memakai faktor diskriminatif.
- HR AI hanya company scope.
- Mentor AI hanya mentee assignment.

Testing:

- HR AI scope test.
- Mentor AI scope test.
- Human review required test.
- AI fairness guard test.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 16 — AI ADMIN & SUPER ADMIN

```text
Kerjakan Batch 16 - AI Admin & Super Admin.

Buat:

- Admin content moderation helper.
- Admin report summary.
- Admin master data suggestion.
- Super Admin audit insight.
- Super Admin security risk summary.
- Super Admin integration diagnostics.
- Super Admin system health assistant.
- AI API endpoints untuk Admin/Super Admin jika relevan.
- docs/batch-reports/batch-16-ai-admin-super-admin.md.

Security:

- AI tidak expose secret.
- AI tidak mengambil tindakan final.
- AI security summary hanya Super Admin authorized.
- Semua AI insight dicatat usage log.
- AI safety event untuk pelanggaran.

Testing:

- Admin AI permission.
- Super Admin AI permission.
- Secret redaction test.
- AI safety event test.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 17 — PRIVACY, CONSENT, FILE SECURITY

```text
Kerjakan Batch 17 - Privacy, Consent, File Security.

Buat:

- Consent management.
- Privacy policy.
- Terms of service.
- Data export request.
- Data deletion request.
- Private document access.
- Signed temporary URL.
- Document access log.
- File scan placeholder.
- Document versioning.
- File security API jika relevan.
- docs/batch-reports/batch-17-privacy-consent-file-security.md.

Security:

- CV tidak public.
- Surat pengantar dan sertifikat private.
- Portfolio bisa public/private.
- HR hanya melihat dokumen kandidat dalam scope.
- Mentor hanya melihat dokumen peserta assigned.
- Semua akses dokumen dicatat.
- File upload divalidasi MIME, extension, size.

Testing:

- Private document access policy.
- Signed URL generation.
- Document access log created.
- Unauthorized document access denied.
- File API response format jika endpoint dibuat.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 18 — ADVANCED RECRUITMENT PIPELINE

```text
Kerjakan Batch 18 - Advanced Recruitment Pipeline.

Buat:

- Custom recruitment pipeline.
- Stage management.
- Kanban HR pipeline.
- Stage transition history.
- Stage notes.
- Screening rubric.
- Screening criteria.
- Candidate score.
- Pipeline SLA reminder.
- Pipeline API jika relevan.
- docs/batch-reports/batch-18-advanced-recruitment-pipeline.md.

Rules:

- Stage transition dicek policy.
- Semua perubahan stage masuk audit log.
- AI score hanya saran, HR wajib review.
- Candidate scoring menyebut faktor digunakan dan tidak digunakan.
- Fairness guard wajib.

Testing:

- Stage transition policy.
- Stage history created.
- Candidate score requires HR review.
- Fairness guard basic test.
- Pipeline API response format jika endpoint dibuat.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 19 — ACTIVITY LOG & AUDIT HARDENING

```text
Kerjakan Batch 19 - Activity Log & Audit Hardening.

Buat:

- Activity log migration final.
- ActivityLogger service final.
- AuditLogger service final.
- SecurityEventLogger final.
- Activity timeline UI.
- Activity filters.
- Log policies.
- Log viewer hardening.
- Log API jika relevan.
- docs/batch-reports/batch-19-activity-audit-hardening.md.

Bedakan:

1. Activity Log = aktivitas operasional manusiawi.
2. Audit Log = aktivitas sensitif untuk compliance/security.
3. Security Event = aktivitas keamanan/risk/login/policy denied.

Pattern wajib:

Action -> Domain Event -> Listener -> Activity Log -> Audit Log jika sensitif -> Security Event jika risk -> Notification/Broadcast.

Testing:

- Activity log created.
- Audit log created.
- Security event created.
- Unauthorized log access denied.
- Log API response format jika endpoint dibuat.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 20 — ATTENDANCE, REALTIME LOCATION, GEOFENCE

```text
Kerjakan Batch 20 - Attendance, Realtime Location, Geofence.

Buat:

- Attendance module.
- Location consent UI.
- Browser geolocation.
- Check-in/check-out.
- Realtime location hanya saat sesi aktif.
- Company geofence.
- Manual correction request.
- Attendance report.
- AttendanceService.
- Redis live location cache dengan TTL.
- Attendance API jika relevan.
- docs/batch-reports/batch-20-attendance-realtime-location-geofence.md.

Privacy:

- Tidak boleh tracking 24 jam.
- Lokasi hanya dengan consent.
- Realtime location hanya saat attendance session.
- Realtime location berhenti otomatis saat check-out.
- Mentor hanya melihat peserta bimbingannya.
- HR hanya melihat peserta company scope-nya.
- Semua akses lokasi sensitif diaudit.

Testing:

- Check-in requires consent.
- Check-out stops realtime location.
- HR scope location access.
- Mentor assignment location access.
- Geofence validation.
- Attendance API response format jika endpoint dibuat.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 21 — NEARBY INTERNSHIP MVP

```text
Kerjakan Batch 21 - Nearby Internship MVP.

Buat:

- Leaflet.js.
- OpenStreetMap.
- Attribution OpenStreetMap.
- Haversine formula.
- Bounding box optimization.
- NearbyInternshipService.
- Nearby search endpoint public.
- Nearby search endpoint user.
- Radius filter.
- Distance sorting.
- Nearby privacy log.
- Nearby rate limit.
- Cache key privacy-safe.
- docs/batch-reports/batch-21-nearby-internship-mvp.md.

Nearby API minimal:

- GET /api/v1/public/nearby-internships
- GET /api/v1/candidate/nearby-internships

Privacy:

- Lokasi user tidak tampil ke HR/Admin/user lain.
- Public nearby search tidak menyimpan koordinat presisi.
- nearby_search_logs hanya menyimpan rounded coordinate/geohash kasar.
- HR hanya melihat analytics agregat.

Testing:

- Haversine calculation.
- Radius filter.
- Rate limit.
- Privacy log rounding.
- Public cannot see user location.
- Nearby API response format.

Update docs/api.md dan docs/openapi.yaml.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 22 — NEARBY MAP & LOCATION PREFERENCE

```text
Kerjakan Batch 22 - Nearby Map & Location Preference.

Buat:

- Map view premium.
- Saved user locations.
- Delete saved location.
- Company location management.
- Admin location verification.
- Cache nearby result.
- Mobile bottom sheet pattern.
- Marker dan radius circle premium.
- Location preference API jika relevan.
- docs/batch-reports/batch-22-nearby-map-location-preference.md.

Privacy:

- Saved location wajib consent.
- Lokasi user tidak bocor ke HR/Admin.
- Company location belum verified mengikuti setting publikasi.
- Cache tidak menyimpan data private user.

Testing:

- User can save location with consent.
- User can delete saved location.
- HR can manage company location in scope.
- Admin can verify location.
- Nearby cache does not cache private user data.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 23 — AI NEARBY RECOMMENDATION

```text
Kerjakan Batch 23 - AI Nearby Recommendation.

Buat:

- AI nearby assistant.
- Nearby recommendation prompt.
- Nearby AI safety rules.
- Nearby AI usage log.
- Nearby AI frontend widget.
- AI nearby API jika relevan.
- docs/batch-reports/batch-23-ai-nearby-recommendation.md.

AI boleh menggunakan:

- Jurusan.
- Skill.
- Preferensi radius.
- Work mode.
- Lokasi kasar/koordinat yang sudah diberi consent.
- Lowongan published.

AI tidak boleh:

- Menyimpan lokasi presisi tanpa consent.
- Menampilkan lokasi user ke HR.
- Menggunakan lokasi untuk diskriminasi.
- Membuat lamaran otomatis.
- Mengambil keputusan final.

Testing:

- Nearby AI requires consent for location context.
- Nearby AI does not expose precise location.
- AI usage log created.
- AI safety guard works.

Update docs/api.md dan docs/openapi.yaml jika endpoint dibuat.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 24 — SYSTEM SERVICE, HEALTH CHECK, QUEUE & SCHEDULER HARDENING

```text
Kerjakan Batch 24 - System Service, Health Check, Queue & Scheduler Hardening.

Tujuan:
Memastikan project benar-benar full web service dengan health check, queue worker, scheduler, status endpoint, dan service monitoring dasar.

Buat:

- HealthCheckService final.
- SystemStatusService.
- QueueHealthService.
- StorageHealthService.
- MailHealthService tanpa expose secret.
- DatabaseHealthService.
- RedisHealthService.
- Endpoint GET /api/v1/health.
- Endpoint GET /api/v1/status.
- Endpoint GET /api/v1/version.
- Endpoint GET /api/v1/features.
- Queue jobs final untuk email OTP, notification, document processing, report generation, external integration sync, AI processing jika aktif.
- Scheduler final untuk close expired internships, cleanup expired OTP, cleanup temp files, interview reminder, notification summary.
- Queue worker documentation.
- Scheduler documentation.
- Supervisor config untuk queue worker jika relevan.
- Docker Compose service untuk queue worker jika relevan.
- docs/health-check.md final.
- docs/queue-scheduler.md final.
- docs/batch-reports/batch-24-system-service-health-queue-scheduler.md.

Security:

- Health check public hanya boleh menampilkan status aman dan tidak membocorkan secret.
- Detail health check sensitif hanya Admin/Super Admin.
- Queue job tidak menyimpan secret di payload.
- Scheduler tidak menjalankan command destruktif.

Testing:

- Health endpoint returns standard response.
- Status endpoint returns safe response.
- Version endpoint returns safe response.
- Queue job dispatch test.
- Scheduler task registration test.
- Secret not exposed in health response.

Update docs/api.md dan docs/openapi.yaml.
Berhenti setelah selesai dan tunggu review saya.
```

## BATCH 25 — PRODUCTION READINESS

```text
Kerjakan Batch 25 - Production Readiness.

Buat:

- Feature flag final.
- System settings final.
- Request ID middleware final.
- Standard error response final.
- Exception renderer final.
- Frontend error handler.
- Accessibility review.
- Demo mode.
- Documentation portal.
- Final security review.
- Deployment guide.
- CI/CD GitHub Actions.
- Final test pass.
- docs/email.md final untuk Resend production setup.
- docs/api.md final.
- docs/openapi.yaml final.
- docs/batch-reports/batch-25-production-readiness.md.

Wajib:

- Request ID muncul di error response.
- Production tidak expose stack trace.
- Demo mode tidak aktif production.
- Feature flags cached via Redis.
- System settings cached via Redis.
- CI menjalankan composer install, npm ci, Pint, PHPStan/Larastan, Pest, TypeScript check, ESLint, build frontend.
- Deployment guide jelas untuk orang awam.
- Resend production email setup terdokumentasi.
- REST API /api/v1 terdokumentasi.
- OpenAPI spec tersedia.
- RESEND_API_KEY tidak masuk log/frontend/repository.
- Database production dijelaskan terpisah dari database development.
- Database development project ini tetap internhub_rekrutmen_2026.
- UI tidak terlihat AI-generated.
- Homepage layak ditunjukkan ke dosen, HR, teman, dan publik.
- README menjelaskan struktur backend/frontend/infrastructure/docs/scripts.
- Documentation portal menjelaskan cara mencari file.

Testing:

- Standard error response.
- Request ID middleware.
- Feature flag helper.
- System setting helper.
- Demo mode disabled in production.
- Accessibility checklist.
- Resend email config safety check.
- Database safety config check.
- Anti AI-looking UI checklist.
- Project structure checklist.
- API documentation checklist.
- Health check checklist.

Berhenti setelah selesai.
```

---

# 13. PROMPT SECURITY REVIEW SETELAH SETIAP BATCH

```text
Lakukan security review untuk batch terakhir.

Periksa:

1. Apakah database aktif benar internhub_rekrutmen_2026?
2. Apakah ada command database berisiko yang dijalankan?
3. Apakah command backend dijalankan dari backend/?
4. Apakah command frontend dijalankan dari frontend/?
5. Apakah command docker dijalankan dari infrastructure/docker/?
6. Apakah ada authorization hanya di frontend?
7. Apakah semua request mutasi memakai Form Request?
8. Apakah Policy/Gate sudah dipakai?
9. Apakah API endpoint sensitif sudah protected?
10. Apakah API response error unauthorized konsisten?
11. Apakah HR bisa mengakses company lain?
12. Apakah Mentor bisa mengakses mentee yang bukan assignment-nya?
13. Apakah ada secret/token/password/API key di log atau frontend?
14. Apakah RESEND_API_KEY aman dan tidak bocor?
15. Apakah Google client secret aman dan tidak bocor?
16. Apakah file kandidat private?
17. Apakah signed URL digunakan untuk file sensitif?
18. Apakah audit log dibuat untuk aksi sensitif?
19. Apakah activity log dibuat untuk aktivitas penting?
20. Apakah security event dibuat untuk login/risk/policy denied?
21. Apakah AI bisa bypass authorization?
22. Apakah lokasi hanya diambil dengan consent?
23. Apakah realtime location berhenti setelah check-out?
24. Apakah nearby search tidak menyimpan koordinat presisi tanpa consent?
25. Apakah query raw SQL aman?
26. Apakah validasi input lengkap?
27. Apakah upload file divalidasi MIME, extension, dan size?
28. Apakah queue dipakai untuk proses berat?
29. Apakah queue payload tidak menyimpan secret?
30. Apakah scheduler tidak menjalankan command destruktif?
31. Apakah health check tidak membocorkan secret?
32. Apakah test tersedia untuk skenario kritikal?

Output:

- Findings
- Severity: Low/Medium/High/Critical
- File terkait
- Rekomendasi fix
- Apakah batch aman lanjut atau harus diperbaiki dulu
```

---

# 14. PROMPT UI/UX WORLD-CLASS REVIEW

```text
Lakukan UI/UX dan design system review kelas dunia untuk halaman dan komponen yang dibuat.

Standar desain:

- world-class
- premium
- sophisticated
- highly-polished
- modern 2026
- clean
- professional
- youth-friendly
- enterprise-grade
- responsive
- accessible
- conversion-focused
- tidak seperti template admin lama
- tidak seperti CRUD demo
- tidak terlihat AI-generated
- human-made
- natural
- trust-building

Khusus anti AI-looking UI, cek:

1. Apakah tampilan terlihat seperti hasil AI template?
2. Apakah terlalu banyak gradient, glassmorphism, blob, atau efek visual generik?
3. Apakah copywriting terdengar seperti AI?
4. Apakah hero image terlihat natural dan relevan?
5. Apakah homepage terasa seperti website rekrutmen magang sungguhan?
6. Apakah dashboard terasa seperti produk nyata, bukan template admin?
7. Apakah ada testimoni, angka, atau logo perusahaan palsu?
8. Apakah spacing, typography, dan hierarchy terasa dibuat oleh designer manusia?
9. Apakah animasi halus atau justru terlalu ramai?
10. Apakah website layak ditunjukkan ke dosen, HR, dan publik tanpa terlihat murahan?

Periksa:

1. Visual hierarchy
2. Typography scale
3. Spacing rhythm
4. Color system
5. Semantic color usage
6. Border radius consistency
7. Shadow/elevation consistency
8. Icon consistency
9. Layout mobile/tablet/desktop
10. Dashboard density
11. Sidebar/topbar polish
12. Landing page conversion flow
13. CTA clarity
14. Card design quality
15. Table responsive behavior
16. Kanban UX untuk HR pipeline
17. Map/list UX untuk nearby search
18. Consent UX untuk location
19. Attendance active state
20. AI assistant interaction quality
21. API loading state
22. API error state
23. Empty state
24. Error state
25. Success state
26. Focus state
27. Keyboard navigation
28. ARIA label
29. Contrast
30. Motion/micro-interaction quality
31. Reusable component consistency

Output:

- Overall UI/UX score 1-10
- Anti AI-looking score 1-10
- Trust score 1-10
- Masalah visual utama
- Masalah design system
- Masalah accessibility
- Masalah responsive
- Komponen terkait
- Rekomendasi perbaikan
- Prioritas fix: Critical/High/Medium/Low
- Apakah layak disebut world-class atau belum
- Apakah masih terlihat AI-generated atau sudah natural
```

---

# 15. PROMPT RECOVERY KALAU ERROR

```text
Stop. Masuk recovery mode.

Jangan lanjut coding.
Jangan menjalankan command baru dulu.
Jangan menghapus file.
Jangan menjalankan command destruktif.
Jangan menjalankan command database destruktif.

Tugas Anda sekarang:

1. Identifikasi batch aktif.
2. Tampilkan root project.
3. Tampilkan folder aktif saat error.
4. Tampilkan file yang sudah dibuat/diubah pada batch ini.
5. Tampilkan command yang sudah dijalankan dan dari folder mana.
6. Tampilkan database aktif.
7. Tampilkan error terakhir secara ringkas.
8. Jelaskan penyebab paling mungkin dengan bahasa sederhana.
9. Buat rencana perbaikan maksimal 5 langkah.
10. Beri tahu command mana yang aman dan mana yang berisiko.
11. Tunggu approval saya sebelum command berisiko.

Jika error artisan, cek:

- Apakah sedang berada di backend/?
- Apakah backend/artisan ada?
- Apakah command dijalankan dari root secara tidak sengaja?
- composer install
- .env
- APP_KEY
- DB connection
- DB_DATABASE harus internhub_rekrutmen_2026
- Redis connection
- storage/bootstrap/cache permission
- config cache

Jika error composer, cek:

- Apakah sedang berada di backend/?
- Apakah backend/composer.json ada?

Jika error npm, cek:

- Apakah sedang berada di frontend/?
- Apakah frontend/package.json ada?
- Node path
- npm path
- node version
- npm version
- package-lock.json
- node_modules
- vite config
- tsconfig

Jika error Vite:

- Apakah frontend/vite.config.ts ada?
- Apakah path input frontend benar?
- Apakah output build ke backend/public/build benar?
- Apakah backend/resources/views/app.blade.php membaca asset yang benar?

Jika error Docker:

- Apakah sedang berada di infrastructure/docker/?
- Apakah docker-compose.yml ada?

Jika error database:

- DB_CONNECTION
- DB_HOST
- DB_PORT
- DB_DATABASE
- DB_USERNAME
- PostgreSQL service
- Docker container jika dipakai
- apakah database internhub_rekrutmen_2026 sudah ada
- jangan memakai database lama
- jangan drop/reset database tanpa approval

Jika error API:

- Apakah endpoint berada di /api/v1?
- Apakah route ada di backend/routes/api.php?
- Apakah controller berada di backend/app/Http/Controllers/Api?
- Apakah response memakai ApiResponse?
- Apakah Form Request valid?
- Apakah Policy/Gate menolak request?
- Apakah middleware auth/role/company scope aktif?
- Apakah exception handler mengubah error menjadi format standar?

Jika error queue:

- Apakah Redis aktif?
- Apakah QUEUE_CONNECTION benar?
- Apakah job payload menyimpan model/id yang benar?
- Apakah worker berjalan?
- Apakah failed_jobs table tersedia?
- Jangan retry job membabi buta sebelum tahu penyebabnya.

Jika error scheduler:

- Apakah schedule didefinisikan di routes/console.php atau console kernel sesuai versi Laravel?
- Apakah command scheduler aman?
- Apakah timezone benar?
- Apakah scheduler tidak menjalankan aksi destruktif?

Jika error email/Resend:

- MAIL_MAILER=resend
- RESEND_API_KEY ada di backend/.env
- RESEND_API_KEY tidak kosong untuk production test
- MAIL_FROM_ADDRESS valid
- domain/sender Resend sudah verified
- config cache sudah clear
- tidak ada MAIL_MAILER=log yang menimpa config

Jangan membuat workaround asal jalan.
Jangan memindahkan file sembarangan.
Jelaskan dulu file mana yang salah lokasi dan rencana perbaikannya.
```

---

# 16. PROMPT FINAL ACCEPTANCE CHECK

```text
Lakukan final acceptance check untuk project InternHub.

Periksa apakah semua acceptance criteria berikut sudah terpenuhi:

1. Database baru internhub_rekrutmen_2026 digunakan.
2. Database lama tidak dipakai.
3. Struktur folder backend/frontend/infrastructure/docs/scripts tersedia.
4. Laravel berada di backend/.
5. backend/artisan tersedia.
6. backend/composer.json tersedia.
7. Frontend berada di frontend/.
8. frontend/package.json tersedia.
9. frontend/resources/js tersedia.
10. frontend/resources/css tersedia.
11. frontend/vite.config.ts tersedia.
12. Docker/infrastructure berada di infrastructure/.
13. Docs berada di docs/.
14. Scripts berada di scripts/.
15. Semua role tersedia.
16. Login email/password berjalan.
17. Register email/password berjalan.
18. Login Google berjalan.
19. Register Google berjalan.
20. Email verification OTP 6 digit menggunakan Resend berjalan.
21. Forgot password menggunakan Resend berjalan.
22. Reset password menggunakan Resend berjalan.
23. Sanctum session auth berjalan.
24. CAPTCHA tersedia.
25. Rate limit berjalan.
26. Login failure logging berjalan.
27. Suspicious login baseline tersedia.
28. Device/session management tersedia.
29. Policy/Gate aktif.
30. Public page tersedia.
31. Homepage terlihat seperti website rekrutmen magang sungguhan.
32. Homepage memiliki hero image/foto/visual bertema magang.
33. Copywriting homepage natural, profesional, dan trust-building.
34. Homepage tidak terlihat AI-generated.
35. Trust section tersedia.
36. How It Works section tersedia.
37. Featured Internship section tersedia.
38. FAQ section tersedia.
39. Final CTA section tersedia.
40. Animasi homepage, tombol, card, dan FAQ terasa halus serta menghormati prefers-reduced-motion.
41. User dashboard tersedia.
42. HR dashboard tersedia.
43. Mentor dashboard tersedia.
44. Admin dashboard tersedia.
45. Super Admin dashboard tersedia.
46. Internship CRUD tersedia.
47. Apply internship tersedia.
48. Applicant pipeline tersedia.
49. Mentor assignment tersedia.
50. AI assistant tersedia per role.
51. AI safety guard aktif.
52. Activity log tersedia per role.
53. Audit log tersedia.
54. Security event tersedia.
55. Realtime notification tersedia.
56. Attendance check-in/check-out tersedia.
57. Realtime location aktif hanya saat sesi attendance.
58. Location consent tersedia.
59. Geofence tersedia.
60. Nearby internship search tersedia.
61. Nearby map menggunakan Leaflet + OpenStreetMap.
62. Nearby distance menggunakan Haversine fallback.
63. External integration skeleton MagangHub tersedia.
64. CSV import external internship tersedia.
65. Queue berjalan.
66. Horizon berjalan.
67. Redis digunakan.
68. PostgreSQL digunakan.
69. Cloudflare R2 siap untuk storage production.
70. UI responsive.
71. UI/UX sudah world-class atau minimal mendekati.
72. PRD tersedia.
73. MCP tersedia.
74. Skill rules tersedia.
75. Batch report tersedia.
76. Test tersedia.
77. README lengkap.
78. Documentation portal tersedia.
79. docs/project-structure.md tersedia.
80. Tidak ada secret/token/password plaintext di log/frontend.
81. RESEND_API_KEY tidak masuk repository/log/frontend.
82. Google client secret tidak masuk repository/log/frontend.
83. Tidak ada testimonial palsu.
84. Tidak ada angka statistik palsu.
85. Tidak ada logo perusahaan palsu.
86. REST API /api/v1 tersedia.
87. ApiResponse helper tersedia.
88. Standard success API response tersedia.
89. Standard error API response tersedia.
90. RequestIdMiddleware tersedia.
91. request_id muncul di error response.
92. docs/api.md tersedia.
93. docs/openapi.yaml tersedia.
94. Health check endpoint tersedia.
95. Health check tidak membocorkan secret.
96. Version/status endpoint tersedia.
97. Queue worker terdokumentasi.
98. Scheduler terdokumentasi.
99. Service/action layer dipakai untuk business logic utama.
100. Controller tetap tipis.
101. API Resource dipakai untuk response JSON penting.
102. Web page dan API endpoint tersedia untuk fitur utama yang relevan.
103. Website layak ditunjukkan ke dosen.
104. Website layak ditunjukkan ke HR/perusahaan.
105. Website layak ditunjukkan ke publik.

Output:

- Passed
- Failed
- Partial
- Evidence/file terkait
- Perbaikan yang wajib sebelum production
- Perbaikan nice-to-have
- Kesimpulan apakah project siap demo/skripsi/production
```
