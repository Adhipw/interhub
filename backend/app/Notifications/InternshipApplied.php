<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Notifications\Messages\MailMessage;

class InternshipApplied extends BaseNotification
{
    public function __construct(public Application $application) {}

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Application: '.$this->application->internship->title)
            ->line('A new candidate has applied for "'.$this->application->internship->title.'".')
            ->line('Candidate: '.$this->application->user->name)
            ->action('Review Application', route('hr.applications.show', $this->application->id))
            ->line('Go to HR dashboard for more details.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'candidate_name' => $this->application->user->name,
            'internship_title' => $this->application->internship->title,
            'message' => 'New application received.',
        ];
    }
}
