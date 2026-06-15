# Authentication Flow - InternHub

## 1. Registrasi / Login Baru
- User memilih **"Daftar dengan Google"** (Socialite) atau **Email/Password**.
- Jika via Email:
  1. Input data diri dasar.
  2. Sistem mengirim 6-digit OTP via **Resend**.
  3. User memasukkan OTP untuk aktivasi akun.
- Akun yang belum terverifikasi OTP tidak dapat mengakses dashboard utama.

## 2. Lupa Password
- User memasukkan email.
- Sistem mengirimkan **Secure Reset Link** dan **OTP Fallback** melalui Resend.
- Reset link akan kedaluwarsa dalam 15 menit.

## 3. Integrasi Resend
- Menggunakan provider Resend untuk pengiriman email transaksional yang andal.
- Template email bersifat minimalis dan profesional.
- `MAIL_MAILER=resend` dikonfigurasi di backend.
