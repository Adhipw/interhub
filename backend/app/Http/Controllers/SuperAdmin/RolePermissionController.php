<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return Inertia::render('SuperAdmin/Roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function storeRole(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);

        $role = Role::create(['name' => $request->name]);

        AuditService::log('super_admin_role_created', $role, "New role created: {$role->name}");

        return redirect()->back()->with('success', 'Role berhasil dibuat.');
    }

    public function syncPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
        ]);

        $role->syncPermissions($request->permissions);

        // Security Event for permission change
        SecurityEvent::create([
            'event_type' => 'permissions_synced',
            'severity' => 'high',
            'user_id' => auth()->id(),
            'description' => "Permissions synced for role: {$role->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => json_encode([
                'role' => $role->name,
                'permissions' => $request->permissions,
            ]),
        ]);

        AuditService::log('super_admin_permissions_sync', $role, "Permissions synced for role: {$role->name}");

        return redirect()->back()->with('success', 'Izin role berhasil diperbarui.');
    }
}
