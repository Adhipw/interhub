# Realtime Notifications - InternHub

## 1. Kanal Notifikasi
- **Database**: Untuk menampilkan notifikasi di pusat notifikasi (UI).
- **Email (Resend)**: Untuk pemberitahuan penting (e.g., Lamaran diterima, OTP, Tugas baru).
- **WebSockets (Laravel Reverb)**: Untuk pengiriman notifikasi secara instan tanpa perlu refresh halaman.

## 2. Kejadian Notifikasi (Events)
- `ApplicationSubmitted`: Notifikasi ke HR saat ada lamaran baru.
- `ApplicationStatusUpdated`: Notifikasi ke Mahasiswa saat status lamaran berubah.
- `MentorFeedbackSubmitted`: Notifikasi ke Mahasiswa saat mentor memberikan masukan.
- `AttendanceAnomalyDetected`: Peringatan ke HR jika user berada di luar radius geofencing saat absensi.
