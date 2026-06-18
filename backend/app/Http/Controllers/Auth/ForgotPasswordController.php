<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Auth\PasswordResetOtpNotification;
use App\Services\Auth\PasswordResetOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendOtp(Request $request, PasswordResetOtpService $otpService)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();
        $otp = $otpService->generate($user->email);

        try {
            $user->notify(new PasswordResetOtpNotification($otp));
        } catch (\Throwable $e) {
            Log::error('Failed to send password reset OTP', [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'email' => 'Kode OTP gagal dikirim ke email. Periksa konfigurasi mailer atau coba lagi.',
            ]);
        }

        return redirect()->route('password.reset', ['email' => $request->email]);
    }
}
