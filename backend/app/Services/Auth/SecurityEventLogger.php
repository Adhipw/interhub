<?php

namespace App\Services\Auth;

use App\Models\SecurityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SecurityEventLogger
{
    public function log(string $eventType, string $description, ?Request $request = null): void
    {
        try {
            $request = $request ?? request();

            SecurityEvent::create([
                'user_id' => Auth::id(),
                'event_type' => $eventType,
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'severity' => 'info',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log security event: '.$e->getMessage());
        }
    }
}
