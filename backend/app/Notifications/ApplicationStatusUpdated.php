<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationStatusUpdated extends BaseNotification
{
    public function __construct(public Application $application) {}

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update on Your Application: '.$this->application->internship->title)
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('The status of your application for "'.$this->application->internship->title.'" has been updated to: '.strtoupper($this->application->status))
            ->action('View Application', route('applications.show', $this->application->id))
            ->line('Thank you for using InternHub!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'internship_title' => $this->application->internship->title,
            'status' => $this->application->status,
            'message' => 'Your application status has been updated.',
        ];
    }
}
