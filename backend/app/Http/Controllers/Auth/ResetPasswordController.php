<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\PasswordResetOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class ResetPasswordController extends Controller
{
    public function show(Request $request)
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request, PasswordResetOtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (! $otpService->verify($request->email, $request->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $otpService->complete($request->email);

        return redirect()->route('login')->with('status', 'Password berhasil diperbarui.');
    }
}
