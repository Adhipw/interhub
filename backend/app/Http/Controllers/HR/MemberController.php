<?php

namespace App\Http\Controllers\HR;

use App\Enums\CompanyRole;
use App\Http\Controllers\Controller;
use App\Models\CompanyMember;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class MemberController extends Controller
{
    public function index()
    {
        $company = app('current_company');
        $members = CompanyMember::where('company_id', $company->id)
            ->with('user')
            ->get();

        return Inertia::render('HR/Team/Index', [
            'members' => $members,
            'roles' => collect(CompanyRole::cases())->map(fn ($role) => [
                'value' => $role->value,
                'label' => $role->label(),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $company = app('current_company');

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => ['required', new Enum(CompanyRole::class)],
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if already a member
        $exists = CompanyMember::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['email' => 'User ini sudah menjadi anggota tim.']);
        }

        $member = CompanyMember::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'role' => $request->role,
            'is_active' => true,
        ]);

        // Sync Spatie role if needed (optional, depends on system design)
        // For now, we trust the CompanyMember role for scope

        AuditService::log('company_member_added', $member, "Member added: {$user->name} as {$request->role}");

        return back()->with('status', 'Anggota tim berhasil ditambahkan.');
    }

    public function update(Request $request, CompanyMember $member)
    {
        // Security check
        $company = app('current_company');
        if ($member->company_id !== $company->id) {
            abort(403);
        }

        $request->validate([
            'role' => ['required', new Enum(CompanyRole::class)],
            'is_active' => 'required|boolean',
        ]);

        $member->update($request->only(['role', 'is_active']));

        AuditService::log('company_member_updated', $member, "Member status/role updated for: {$member->user->name}");

        return back()->with('status', 'Data anggota tim diperbarui.');
    }

    public function destroy(CompanyMember $member)
    {
        $company = app('current_company');
        if ($member->company_id !== $company->id) {
            abort(403);
        }

        // Don't allow deleting self?
        if ($member->user_id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak bisa menghapus diri sendiri.']);
        }

        AuditService::log('company_member_removed', $member, "Member removed: {$member->user->name}");

        $member->delete();

        return back()->with('status', 'Anggota tim berhasil dihapus.');
    }
}
