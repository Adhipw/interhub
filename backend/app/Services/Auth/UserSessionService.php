<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserSessionService
{
    public function getActiveSessions()
    {
        return DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->get();
    }

    public function logoutOtherDevices(string $currentSessionId)
    {
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }
}
