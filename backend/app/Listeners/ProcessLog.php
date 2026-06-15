<?php

namespace App\Listeners;

use App\Interfaces\LoggableEvent;
use App\Models\User;
use App\Notifications\SecurityAlert;
use App\Services\Logging\ActivityLogger;
use App\Services\Logging\AuditLogger;
use App\Services\Logging\SecurityEventLogger;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessLog implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(LoggableEvent $event): void
    {
        // 1. Activity Log (Operational)
        ActivityLogger::log(
            $event->getLogAction(),
            $event->getLogDescription(),
            $event->getLogSubject(),
            $event->getLogProperties()
        );

        // 2. Audit Log (Sensitive/Compliance)
        if ($event->isSensitive()) {
            AuditLogger::log(
                $event->getLogAction(),
                $event->getLogSubject(),
                $event->getLogProperties()['old'] ?? null,
                $event->getLogProperties()['new'] ?? null,
                $event->getLogDescription()
            );
        }

        // 3. Security Event (Risk)
        if ($event->isSecurityRisk()) {
            SecurityEventLogger::log(
                $event->getLogAction(),
                $event->getLogDescription(),
                $event->getLogSubject()->id
            );

            // Notify the user about security risk
            $user = $event->getLogSubject();
            if ($user instanceof User) {
                $user->notify(new SecurityAlert($event->getLogAction(), $event->getLogDescription()));
            }
        }

        // 4. Broadcast could be added here if needed
    }
}
