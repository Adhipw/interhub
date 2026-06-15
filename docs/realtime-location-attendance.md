# Geolocation & Nearby Internship - InternHub

## 1. Nearby Search (Pencarian Terdekat)
- Menggunakan koordinat lokasi user (jika diizinkan).
- Perhitungan jarak menggunakan **Haversine Formula** di tingkat database (PostgreSQL) atau Service.
- UI Map menggunakan **Leaflet.js** dengan tile provider **OpenStreetMap**.

## 2. Realtime Attendance (Absensi Magang)
- HR menentukan radius absensi (Geofencing) untuk setiap lokasi magang.
- User melakukan *Check-in* melalui aplikasi.
- Selama status *Check-in* aktif, koordinat lokasi dikirim secara berkala (Realtime Tracking) menggunakan **Laravel Reverb**.
- Tracking berhenti otomatis saat *Check-out* atau sesi magang berakhir.

## 3. Privasi Lokasi
- Data koordinat presisi hanya disimpan saat sesi absensi aktif.
- Data riwayat lokasi lama akan dianonimisasi atau dibulatkan untuk keperluan analitik.
