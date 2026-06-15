# Database Isolation Strategy - InternHub

## 1. Nama Database
Seluruh data proyek InternHub akan disimpan dalam database: `internhub_rekrutmen_2026`.

## 2. Protokol Isolasi
- **Zero Contact**: Dilarang melakukan query silang ke database lain di server yang sama.
- **Connection Configuration**: Backend Laravel dikonfigurasi secara statis ke DB ini untuk menghindari kesalahan environment.
- **No Destructive Commands**: Perintah `migrate:fresh` atau `db:wipe` memerlukan approval manual melalui Terminal Review.

## 3. Verifikasi
Sebelum setiap migrasi, sistem akan mencetak variabel `DB_DATABASE` untuk memastikan target sudah benar.
