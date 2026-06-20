<?php

namespace App\Http\Controllers\Api;

use App\Enums\Permission as PermissionEnum;
use App\Enums\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ApiRoleController extends ApiBaseController
{
    /**
     * Display a listing of roles.
     */
    public function index(): JsonResponse
    {
        $this->ensureDefaultRolesAndPermissions();

        $roles = Role::with('permissions')->get();

        return $this->sendResponse($roles, 'Roles retrieved successfully');
    }

    /**
     * Display a listing of permissions.
     */
    public function permissions(): JsonResponse
    {
        $this->ensureDefaultRolesAndPermissions();

        $permissions = Permission::all();

        return $this->sendResponse($permissions, 'Permissions retrieved successfully');
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return $this->sendResponse($role->load('permissions'), 'Role created successfully');
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $validated = $validator->validated();

        if (array_key_exists('name', $validated) && $validated['name'] !== $role->name) {
            $role->name = $validated['name'];
            $role->save();
        }

        if (array_key_exists('permissions', $validated)) {
            $role->syncPermissions($validated['permissions']);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return $this->sendResponse($role->load('permissions'), 'Role updated successfully');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): JsonResponse
    {
        if ($role->name === 'super_admin') {
            return $this->sendError('Forbidden', ['error' => 'Super Admin role cannot be deleted'], 403);
        }

        $role->delete();

        return $this->sendResponse(null, 'Role deleted successfully');
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
