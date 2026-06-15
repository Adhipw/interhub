<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('roles')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    public function toggleStatus(User $user)
    {
        $this->authorize('moderate', $user);

        $user->is_active = ! $user->is_active;
        $user->save();

        AuditService::log(
            'admin_user_moderation',
            $user,
            'User status toggled to: '.($user->is_active ? 'Active' : 'Inactive')
        );

        return redirect()->back()->with('success', 'Status user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $userName = $user->name;
        $user->delete();

        AuditService::log('admin_user_deleted', null, "User deleted: {$userName}");

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
