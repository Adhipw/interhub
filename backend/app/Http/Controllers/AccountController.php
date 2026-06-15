<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNameRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Services\Logging\ActivityLogger;
use App\Services\Logging\SecurityEventLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function __construct(
        protected ActivityLogger $activityLogger,
        protected SecurityEventLogger $securityLogger
    ) {}

    public function index()
    {
        return Inertia::render('Settings/Account', [
            'user' => Auth::user(),
        ]);
    }

    public function updateName(UpdateNameRequest $request)
    {
        $user = $request->user();
        $oldName = $user->name;
        $newName = trim(strip_tags($request->name));

        $user->name = $newName;
        $user->save();

        $this->activityLogger->log('name_updated', "Nama diubah dari '{$oldName}' menjadi '{$newName}'");

        return back()->with('status', 'name-updated');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        // Security Check: Old password must match
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        // Security Check: New password cannot be the same as old
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password baru tidak boleh sama dengan password lama.',
            ]);
        }

        // Update Password
        $user->password = Hash::make($request->password);
        $user->save();

        // Security Event
        $this->securityLogger->log('password_changed', 'Password user berhasil diubah melalui pengaturan akun');

        // Regenerate Session for extra security
        $request->session()->regenerate();

        return back()->with('status', 'password-updated');
    }
}
