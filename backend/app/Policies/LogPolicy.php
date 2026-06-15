<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class LogPolicy
{
    /**
     * Determine whether the user can view any logs.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN->value);
    }

    /**
     * Determine whether the user can view the activity log.
     */
    public function viewActivity(User $user): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN->value) || $user->hasRole(UserRole::ADMIN->value);
    }

    /**
     * Determine whether the user can view audit logs.
     */
    public function viewAudit(User $user): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN->value);
    }

    /**
     * Determine whether the user can view security events.
     */
    public function viewSecurity(User $user): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN->value);
    }
}
