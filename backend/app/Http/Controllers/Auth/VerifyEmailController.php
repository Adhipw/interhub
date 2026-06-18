<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Notifications\Auth\EmailVerificationOtpNotification;
use App\Services\Auth\EmailVerificationOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class VerifyEmailController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Roles that bypass OTP/Verification
        $bypassRoles = [
            UserRole::SUPER_ADMIN->value,
            UserRole::ADMIN->value,
            UserRole::HR->value,
            UserRole::MENTOR->value,
        ];

        if ($user->hasRole($bypassRoles) || $user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Auth/VerifyEmail', [
            'devOtp' => app()->environment(['local', 'testing'])
                ? session('dev_email_verification_otp')
                : null,
            'deliveryError' => session('otp_delivery_error'),
        ]);
    }

    public function verify(Request $request, EmailVerificationOtpService $otpService)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if ($otpService->verify($request->user()->email, $request->otp)) {
            $request->session()->forget('dev_email_verification_otp');
            $request->user()->markEmailAsVerified();

            return redirect()->route('dashboard', ['verified' => 1]);
        }

        return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluwarsa.']);
    }

    public function resend(Request $request, EmailVerificationOtpService $otpService)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        try {
            $otp = $otpService->generate($request->user()->email);
            $request->user()->notify(new EmailVerificationOtpNotification($otp));

            $request->session()->forget('dev_email_verification_otp');

            if (app()->environment(['local', 'testing'])) {
                $request->session()->put('dev_email_verification_otp', $otp);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to resend email verification OTP', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'resend' => 'Kode OTP gagal dikirim ke email. Periksa konfigurasi mailer atau coba lagi.',
            ]);
        }

        return back()->with('status', 'verification-link-sent');
    }
}
