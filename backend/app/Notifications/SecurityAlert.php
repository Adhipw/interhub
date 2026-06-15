<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecurityAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public $event;

    public $description;

    public function __construct(string $event, string $description)
    {
        $this->event = $event;
        $this->description = $description;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('Security Alert: '.$this->event)
            ->line('A security-related event was detected on your account.')
            ->line('Event: '.$this->event)
            ->line('Description: '.$this->description)
            ->line('If this was not you, please contact support immediately.')
            ->action('View Account Security', url('/profile'));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'security_alert',
            'title' => 'Security Alert: '.$this->event,
            'message' => $this->description,
            'action_url' => '/profile',
        ];
    }
}
