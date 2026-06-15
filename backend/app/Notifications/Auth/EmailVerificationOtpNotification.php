<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kode Verifikasi InterHub')
            ->greeting('Halo '.$notifiable->name.'!')
            ->line('Terima kasih telah mendaftar di InterHub.')
            ->line('Gunakan kode OTP berikut untuk memverifikasi akun Anda:')
            ->line('**'.$this->otp.'**')
            ->line('Kode ini akan kadaluwarsa dalam 10 menit.')
            ->line('Jika Anda tidak merasa mendaftar, abaikan email ini.');
    }
}
