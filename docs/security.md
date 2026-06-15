# Security & Privacy Protocol - InternHub

## 1. Autentikasi
- **Sanctum Session**: Digunakan untuk stateful authentication.
- **CSRF Protection**: Wajib aktif untuk semua request mutasi.
- **Session Rotation**: Sesi diputar otomatis setelah login/logout.

## 2. Proteksi Data
- **No Plaintext**: Password, token, dan OTP wajib di-hash sebelum disimpan.
- **API Key Safety**: `RESEND_API_KEY` dan `GOOGLE_CLIENT_SECRET` dilarang masuk ke log atau frontend.

## 3. Privasi Lokasi
- **Consent Based**: Lokasi hanya diakses jika user memberikan izin (browser prompt).
- **Session Only**: Tracking lokasi realtime hanya aktif selama sesi absensi magang (`attendance_sessions`).

## 4. Keamanan Berkas
- **Private Storage**: CV dan dokumen lamaran disimpan di folder private (R2/Local Private).
- **Signed URL**: Akses berkas hanya via temporary signed URL (e.g., expired dalam 10 menit).

## 5. Audit & Security Event
- Setiap kegagalan login dan akses ditolak (Policy Denied) wajib dicatat dalam `security_events`.
