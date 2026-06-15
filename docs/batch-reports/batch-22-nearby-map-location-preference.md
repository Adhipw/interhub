# Batch 22 - Nearby Map & Location Preference

## Overview
Peningkatan fitur Nearby dengan antarmuka peta interaktif dan manajemen preferensi lokasi pengguna.

## Key Deliverables

### 1. Map View Premium
- **Interactive Map**: Integrasi Leaflet.js dengan layer OpenStreetMap untuk visualisasi lowongan.
- **Marker & Radius**: Marker premium untuk lokasi perusahaan dan lingkaran (circle) transparan untuk menunjukkan radius pencarian.
- **Bottom Sheet Pattern**: Desain mobile-friendly dengan panel bawah yang dapat digeser (bottom sheet) untuk menampilkan detail lowongan di atas peta.

### 2. User Location Management
- **Saved Locations**: Pengguna dapat menyimpan lokasi penting (Rumah, Kampus, dsb) untuk memudahkan pencarian rutin.
- **Primary Location**: Opsi untuk menetapkan satu lokasi sebagai default pencarian.
- **Consent-First**: Penyimpanan koordinat lokasi wajib mendapatkan persetujuan eksplisit pengguna.

### 3. Company & Admin Workflow
- **Location Verification**: Admin dapat memverifikasi koordinat perusahaan untuk memastikan keakuratan data geofencing.
- **Publication Control**: Lokasi perusahaan yang belum diverifikasi dapat dibatasi publikasinya sesuai kebijakan privasi.

### 4. Performance Optimization
- **Nearby Cache**: Hasil pencarian nearby di-cache berdasarkan koordinat yang dibulatkan untuk mempercepat loading tanpa menyimpan data pribadi pengguna dalam cache.

## Privacy & Security
- **Data Isolation**: Lokasi yang disimpan pengguna tidak dapat diakses oleh HR atau Admin tanpa izin khusus.
- **Cache Integrity**: Cache tidak menyimpan identitas pengguna, hanya memetakan area geografis ke daftar lowongan.

## Testing Verification
- [x] **Save location with consent**: Verifikasi record tersimpan hanya setelah klik setuju.
- [x] **Delete saved location**: Pastikan data terhapus permanen dari database.
- [x] **Admin verification**: Status `is_verified` pada koordinat perusahaan dapat diubah oleh admin.
- [x] **Mobile UX**: Bottom sheet berfungsi dengan baik pada resolusi layar kecil.

## Conclusion
Batch 22 membawa pengalaman navigasi yang intuitif dan personal bagi pengguna dalam mencari peluang magang terdekat.
