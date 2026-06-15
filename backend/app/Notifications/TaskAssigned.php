<?php

namespace App\Notifications;

use App\Models\MentorTask;
use Illuminate\Notifications\Messages\MailMessage;

class TaskAssigned extends BaseNotification
{
    public function __construct(public MentorTask $task) {}

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Task Assigned: '.$this->task->title)
            ->line('You have a new task assigned by your mentor.')
            ->line('Task: '.$this->task->title)
            ->action('View Task', route('dashboard'))
            ->line('Deadline: '.($this->task->deadline_at?->format('Y-m-d') ?? 'No deadline'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'message' => 'New task assigned to you.',
        ];
    }
}
