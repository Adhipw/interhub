<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ApiAdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->where('role', '!=', 'super_admin');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->status !== null) {
            $query->where('is_active', (bool) $request->status);
        }

        $users = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $users,
        ]);
    }

    public function toggleStatus(User $user)
    {
        if ($user->role === 'super_admin' || $user->hasRole('super_admin')) {
            return response()->json(['status' => 'error', 'message' => 'Cannot modify super admin'], 403);
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User status updated',
            'data' => $user,
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->role === 'super_admin' || $user->hasRole('super_admin')) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete super admin'], 403);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted',
        ]);
    }
}
