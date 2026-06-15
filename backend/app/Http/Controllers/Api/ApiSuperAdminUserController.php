<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ApiSuperAdminUserController extends ApiBaseController
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        $query = User::with('roles'); // Eager load roles for accuracy

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('roles', function ($sub) use ($request) {
                    $sub->where('name', $request->role);
                })->orWhere('role', $request->role);
            });
        }

        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(15);

        return $this->sendResponse([
            'users' => $users,
            'roles' => ['super_admin', 'admin', 'hr', 'mentor', 'user'],
        ], 'Users retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'hr', 'mentor', 'user', 'super_admin'])],
            'avatar' => 'nullable|image|max:2048',
        ]);


        $user = new User;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role']; // Column
        $user->is_active = true;
        $user->email_verified_at = now();
        $user->save();

        if ($request->hasFile('avatar')) {
            $path = $this->fileService->uploadPublic($request->file('avatar'), 'avatars');
            $user->avatar = $path;
            $user->save();
        }

        // Sync with Spatie Roles
        $user->syncRoles([$validated['role']]);

        return $this->sendResponse($user->load('roles'), 'User created and role synced successfully');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['sometimes', Rule::in(['admin', 'hr', 'mentor', 'user', 'super_admin'])],
            'is_active' => 'sometimes|boolean',
            'avatar' => 'nullable|image|max:2048',
        ]);


        if ($request->has('role')) {
            $user->role = $request->role; // Force update the column
            $user->syncRoles([$request->role]); // Sync Spatie
        }

        $user->fill($validated);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $this->fileService->delete($user->avatar);
            }
            $path = $this->fileService->uploadPublic($request->file('avatar'), 'avatars');
            $user->avatar = $path;
        }

        $user->save();


        return $this->sendResponse($user->load('roles'), 'User updated successfully');
    }

    public function ban(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return $this->sendError('Cannot ban yourself', [], 403);
        }

        $user->update([
            'is_active' => false,
            'banned_at' => now(),
            'banned_reason' => $request->reason ?? 'Pelanggaran kebijakan platform.',
        ]);

        return $this->sendResponse($user, 'User has been banned');
    }

    public function unban(User $user)
    {
        $user->update([
            'is_active' => true,
            'banned_at' => null,
            'banned_reason' => null,
        ]);

        return $this->sendResponse($user, 'User has been unbanned');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete yourself'], 403);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted permanently',
        ]);
    }
}
