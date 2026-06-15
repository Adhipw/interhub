# Batch 20 - Attendance, Realtime Location, Geofence

## Overview
Implementasi sistem absensi berbasis lokasi (geofencing) dengan pelacakan lokasi realtime selama sesi aktif dan perlindungan privasi yang ketat.

## Key Deliverables

### 1. Attendance Module
- **Check-in/Check-out**: Sistem absensi harian yang mencatat waktu dan koordinat lokasi.
- **Status Tracking**: Otomatisasi status kehadiran (present, late).
- **History**: Rekam jejak absensi bagi peserta magang, mentor, dan HR.

### 2. Geofencing & Location Verification
- **Company Geofence**: HR dapat mengatur koordinat kantor dan radius (default 100m).
- **Location Validation**: Check-in hanya diizinkan jika pengguna berada di dalam radius geofence yang ditentukan.
- **Browser Geolocation**: Integrasi dengan API Geolocation browser untuk pengambilan koordinat presisi.

### 3. Realtime Location Tracking (Privacy-First)
- **Active Session Only**: Pelacakan lokasi hanya aktif setelah check-in dan berhenti otomatis saat check-out.
- **Redis Caching**: Lokasi live disimpan di Redis dengan TTL (Time To Live) singkat untuk efisiensi dan keamanan.
- **No 24h Tracking**: Sistem tidak memiliki kemampuan teknis untuk melacak pengguna di luar jam kerja/sesi aktif.

### 4. Privacy & Access Control
- **Location Consent**: UI khusus yang mewajibkan persetujuan pengguna sebelum akses lokasi dimulai.
- **Scoped Visibility**: 
    - **Mentor**: Hanya melihat lokasi live dan riwayat peserta bimbingannya.
    - **HR**: Hanya melihat data dalam lingkup perusahaannya.
- **Audit Logs**: Setiap akses lokasi sensitif dicatat dalam audit log (siapa melihat lokasi siapa).

### 5. Manual Correction Request
- **Workflow**: Peserta dapat mengajukan koreksi absensi jika terjadi kendala teknis (misal: GPS error).
- **Approval**: HR meninjau dan menyetujui/menolak permintaan koreksi.

## Technical Details

### Database Schema
- `attendances`: Menyimpan data utama sesi kehadiran.
- `attendance_corrections`: Menyimpan permintaan perbaikan data.
- `companies` (update): Penambahan kolom `latitude`, `longitude`, dan `geofence_radius`.

### Redis Cache Key
- `user_location:{user_id}`: Menyimpan koordinat terakhir selama sesi aktif (TTL 5 menit).

## Testing Verification
- [x] **Check-in requires consent**: Verifikasi UI mewajibkan klik "Setujui".
- [x] **Geofence validation**: Test check-in dari luar radius (422 error).
- [x] **Check-out stops tracking**: Verifikasi Redis key dihapus saat check-out.
- [x] **HR scope access**: Pastikan HR perusahaan A tidak bisa melihat absensi perusahaan B.
- [x] **Audit log creation**: Verifikasi log `attendance_check_in` tersimpan dengan koordinat.

## Conclusion
Sistem absensi ini memastikan akuntabilitas peserta magang dengan tetap menjunjung tinggi privasi individu dan keamanan data lokasi.
