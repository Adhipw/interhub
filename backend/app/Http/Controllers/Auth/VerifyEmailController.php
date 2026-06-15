<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Notifications\Auth\EmailVerificationOtpNotification;
use App\Services\Auth\EmailVerificationOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return redirect()->intended(route('dashboard'));
        }

        return Inertia::render('Auth/VerifyEmail');
    }

    public function verify(Request $request, EmailVerificationOtpService $otpService)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if ($otpService->verify($request->user()->email, $request->otp)) {
            $request->user()->markEmailAsVerified();

            return redirect()->intended(route('dashboard').'?verified=1');
        }

        return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluwarsa.']);
    }

    public function resend(Request $request, EmailVerificationOtpService $otpService)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        try {
            $otp = $otpService->generate($request->user()->email);
            $request->user()->notify(new EmailVerificationOtpNotification($otp));
        } catch (\Exception $e) {
            return back()->withErrors(['resend' => $e->getMessage()]);
        }

        return back()->with('status', 'verification-link-sent');
    }
}
