# Activity, Audit, & Security Logs - InternHub

## 1. Activity Log
- Mencatat aksi rutin user (e.g., melamar magang, mengubah foto profil, posting lowongan).
- Digunakan untuk menampilkan riwayat aktivitas di dashboard user.

## 2. Audit Log (Sensitif)
- Mencatat perubahan data krusial (e.g., mengubah status lamaran, menghapus user, mengubah pengaturan sistem).
- Wajib mencatat: Siapa, Melakukan Apa, Terhadap Apa, Kapan, dan IP Address.

## 3. Security Event
- Mencatat potensi ancaman (e.g., Brute force login, akses unauthorized ke API, SQL injection attempts).
- Memicu peringatan otomatis ke Super Admin via email/notification.
