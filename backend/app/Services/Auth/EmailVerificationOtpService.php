<?php

namespace App\Services\Auth;

use App\Models\EmailVerificationOtp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class EmailVerificationOtpService
{
    public function generate(string $email): string
    {
        $existing = EmailVerificationOtp::where('email', $email)->first();

        if ($existing && $existing->created_at->addMinute()->isFuture()) {
            throw new \Exception('Silakan tunggu 60 detik sebelum mengirim ulang kode OTP.');
        }

        // Delete old OTPs for this email
        EmailVerificationOtp::where('email', $email)->delete();

        $otp = (string) rand(100000, 999999);

        EmailVerificationOtp::create([
            'email' => $email,
            'otp' => Hash::make($otp),
            'expires_at' => Carbon::now()->addMinutes(10),
            'attempts' => 0,
        ]);

        return $otp;
    }

    public function verify(string $email, string $otp): bool
    {
        $record = EmailVerificationOtp::where('email', $email)
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
            $record->delete();

            return true;
        }

        $record->increment('attempts');

        return false;
    }
}
