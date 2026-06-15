# Local Network & Demo Guide - InternHub

## 1. Mengakses dari Perangkat Lain (LAN/WiFi)
Agar proyek bisa dibuka dari HP atau tablet di jaringan yang sama:

1. Dapatkan IP Address komputer Anda (misal: `192.168.1.10`).
2. Jalankan server Laravel:
   ```bash
   php artisan serve --host=0.0.0.0
   ```
3. Jalankan Vite (Frontend):
   ```bash
   npm run dev -- --host
   ```
4. Buka `http://192.168.1.10:8000` di HP Anda.

## 2. Temporary Tunnel (Expose Online)
Jika ingin menunjukkan progres ke orang lain melalui internet tanpa deploy:
- Gunakan **ngrok** atau **Localhost.run**.
- Contoh: `ssh -R 80:localhost:8000 nokey@localhost.run`.
