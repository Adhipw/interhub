# ADR 0017: Use Separated Backend & Frontend Folder Structure

## Status
Proposed

## Context
Proyek InternHub membutuhkan struktur yang rapi agar pengembangan backend (Laravel) dan frontend (Vue) tidak saling tumpang tindih, namun tetap dalam satu repositori (Monorepo) untuk kemudahan manajemen.

## Decision
Kita akan menggunakan struktur folder terpisah di root project:
- `backend/`: Khusus untuk Laravel 13, Controllers, Models, dan Migrations.
- `frontend/`: Khusus untuk Vue 3, Vite, Tailwind 4, dan Components.

## Consequences
- Semua command `php artisan` harus dijalankan dengan masuk ke folder `backend/`.
- Semua command `npm` harus dijalankan dengan masuk ke folder `frontend/`.
- Membutuhkan konfigurasi Vite agar hasil build (dist) diarahkan ke `backend/public/build`.
