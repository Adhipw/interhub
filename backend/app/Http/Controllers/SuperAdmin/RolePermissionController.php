<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\Permission as PermissionEnum;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionController extends Controller
{
    public function index()
    {
        $this->ensureDefaultRolesAndPermissions();

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

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

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

    private function ensureDefaultRolesAndPermissions(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (PermissionEnum::cases() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission->value,
                'guard_name' => 'web',
            ]);
        }

        foreach (UserRole::cases() as $role) {
            Role::firstOrCreate([
                'name' => $role->value,
                'guard_name' => 'web',
            ]);
        }
    }
}
