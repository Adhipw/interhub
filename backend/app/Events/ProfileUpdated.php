<?php

namespace App\Events;

use App\Interfaces\LoggableEvent;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfileUpdated implements LoggableEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $oldData;

    public $newData;

    public function __construct(User $user, array $oldData, array $newData)
    {
        $this->user = $user;
        $this->oldData = $oldData;
        $this->newData = $newData;
    }

    public function getLogAction(): string
    {
        return 'profile_updated';
    }

    public function getLogDescription(): string
    {
        return "User {$this->user->name} updated their profile.";
    }

    public function getLogSubject()
    {
        return $this->user;
    }

    public function getLogProperties(): array
    {
        return [
            'old' => $this->oldData,
            'new' => $this->newData,
        ];
    }

    public function isSensitive(): bool
    {
        return true; // Profile update is sensitive
    }

    public function isSecurityRisk(): bool
    {
        // For demonstration, let's say it's not a security risk unless email changed
        return isset($this->oldData['email']) && $this->oldData['email'] !== $this->newData['email'];
    }
}
