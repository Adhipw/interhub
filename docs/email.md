# Resend Production Setup

## Overview
InternHub menggunakan **Resend** sebagai penyedia layanan email transaksional (OTP, Notifikasi, Alert). Panduan ini menjelaskan langkah-langkah untuk beralih dari mode development ke produksi.

## 1. Registrasi & Verifikasi Domain
1. Buat akun di [Resend](https://resend.com).
2. Tambahkan domain Anda di menu **Domains**.
3. Verifikasi DNS records (SPF, DKIM, DMARC) sesuai instruksi di dashboard Resend.
    - *Penting: Tanpa verifikasi domain, email Anda kemungkinan besar masuk spam.*

## 2. API Key Management
1. Generate **Production API Key** di dashboard Resend.
2. Tambahkan ke file `.env` di server produksi:
    ```env
    RESEND_API_KEY=re_1234567890abcdef
    MAIL_MAILER=resend
    ```
3. **PENTING**: Jangan pernah menyimpan API Key ini di dalam repository Git. Gunakan *Secret Manager* atau variabel lingkungan server.

## 3. Konfigurasi Backend
Pastikan `AppServiceProvider` telah mendaftarkan transport Resend:

```php
$this->app->extend('mail.manager', function ($manager) {
    $manager->extend('resend', function () {
        return new \App\Mail\Transport\ResendTransport(
            config('services.resend.key')
        );
    });
    return $manager;
});
```

## 4. Keamanan
- **Rate Limiting**: Secara default Resend memiliki limit pengiriman. Pastikan queue worker Anda terkonfigurasi untuk menangani throttling jika volume email sangat besar.
- **Logging**: Jangan log konten email yang mengandung OTP atau data pribadi sensitif.

## 5. Monitoring
Gunakan dashboard Resend untuk memantau:
- **Delivery Rate**: Persentase email yang sampai ke tujuan.
- **Bounce Rate**: Email yang gagal dikirim (jika terlalu tinggi, reputasi domain terancam).
- **Open Rate**: Efektivitas notifikasi.
