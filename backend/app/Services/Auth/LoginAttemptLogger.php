<?php

namespace App\Services\Auth;

use App\Models\LoginAttempt;
use Illuminate\Http\Request;

class LoginAttemptLogger
{
    public function log(Request $request, ?int $userId, string $email, bool $isSuccessful): void
    {
        LoginAttempt::create([
            'user_id' => $userId,
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_successful' => $isSuccessful,
        ]);
    }
}
