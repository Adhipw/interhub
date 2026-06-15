<?php

namespace App\Services\Auth;

use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;

class SuspiciousLoginService
{
    public function isSuspicious(User $user, Request $request): bool
    {
        // Check if user has logged in from this IP before
        $previousLogin = LoginAttempt::where('user_id', $user->id)
            ->where('is_successful', true)
            ->where('ip_address', $request->ip())
            ->exists();

        if (! $previousLogin && LoginAttempt::where('user_id', $user->id)->where('is_successful', true)->exists()) {
            // New IP for an existing user who has logged in before
            return true;
        }

        return false;
    }
}
