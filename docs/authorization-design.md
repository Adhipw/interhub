# Authorization Design - InternHub

## 1. Pendekatan
Menggunakan **Laravel Policies** sebagai satu-satunya sumber kebenaran untuk logika otorisasi.

## 2. Contoh Kebijakan (Policies)
- `InternshipPolicy`: Hanya HR perusahaan pemilik lowongan yang boleh mengedit.
- `ApplicationPolicy`: Hanya kandidat pemilik lamaran atau HR perusahaan terkait yang boleh melihat detail dokumen private.
- `MentorPolicy`: Hanya mentor yang ditugaskan yang boleh memberikan nilai pada mentee tertentu.

## 3. Middleware
Semua route dashboard dilindungi oleh middleware `auth` dan `verified` (memastikan OTP sudah tuntas).
