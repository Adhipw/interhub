<?php

namespace App\Services\Auth;

use App\Models\PasswordResetOtp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PasswordResetOtpService
{
    public function generate(string $email): string
    {
        PasswordResetOtp::where('email', $email)->delete();

        $otp = (string) rand(100000, 999999);

        PasswordResetOtp::create([
            'email' => $email,
            'otp' => Hash::make($otp),
            'expires_at' => Carbon::now()->addMinutes(15),
            'attempts' => 0,
        ]);

        return $otp;
    }

    public function verify(string $email, string $otp): bool
    {
        $record = PasswordResetOtp::where('email', $email)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $record) {
            return false;
        }

        if ($record->attempts >= 5) {
            $record->delete();

            return false;
        }

        if (Hash::check($otp, $record->otp)) {
            return true;
        }

        $record->increment('attempts');

        return false;
    }

    public function complete(string $email): void
    {
        PasswordResetOtp::where('email', $email)->delete();
    }
}
