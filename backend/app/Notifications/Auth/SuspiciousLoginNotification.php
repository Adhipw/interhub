<?php

namespace App\Notifications\Auth;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspiciousLoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected array $details) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Peringatan Keamanan: Login Baru Terdeteksi')
            ->greeting('Halo '.$notifiable->name.'!')
            ->line('Kami mendeteksi login baru ke akun InternHub Anda dari perangkat atau lokasi yang tidak dikenal.')
            ->line('**Detail Login:**')
            ->line('Waktu: '.Carbon::now()->toDayDateTimeString())
            ->line('IP Address: '.$this->details['ip'])
            ->line('Perangkat: '.$this->details['user_agent'])
            ->line('Jika ini adalah Anda, Anda bisa mengabaikan email ini. Jika bukan, segera amankan akun Anda dengan mengganti password.');
    }
}
