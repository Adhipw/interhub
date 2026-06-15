<?php

namespace App\Services\Auth;

use App\Enums\CompanyRole;
use App\Enums\UserRole;
use App\Models\User;

class RoleResolver
{
    /**
     * Resolve the global role of the user.
     */
    public function getGlobalRole(User $user): ?UserRole
    {
        // Spatie roles are global in this setup
        $roleName = $user->getRoleNames()->first();

        return $roleName ? UserRole::tryFrom($roleName) : null;
    }

    /**
     * Check if user has a specific global role.
     */
    public function hasGlobalRole(User $user, UserRole $role): bool
    {
        return $user->hasRole($role->value);
    }

    /**
     * Resolve user role within a specific company.
     * (Placeholder for Batch 5/6 where CompanyUser model exists)
     */
    public function getCompanyRole(User $user, int $companyId): ?CompanyRole
    {
        // For now, return null as we haven't built the CompanyUser pivot
        return null;
    }
}
