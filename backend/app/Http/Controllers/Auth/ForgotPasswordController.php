<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Auth\PasswordResetOtpNotification;
use App\Services\Auth\PasswordResetOtpService;
use Illuminate\Http\Request;
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

        $user->notify(new PasswordResetOtpNotification($otp));

        return redirect()->route('password.reset', ['email' => $request->email]);
    }
}
