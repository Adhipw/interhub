<?php

namespace App\Services\Logging;

use App\Models\SecurityEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SecurityEventLogger
{
    /**
     * Log a security or risk-related event.
     */
    public static function log(string $eventType, ?string $description = null, mixed $userId = null): SecurityEvent
    {
        return SecurityEvent::create([
            'user_id' => $userId ?? Auth::id(),
            'event_type' => $eventType,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
