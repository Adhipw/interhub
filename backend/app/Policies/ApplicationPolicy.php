<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function before(User $user, $ability)
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Application $application): bool
    {
        // Student can view own application
        if ($user->id === $application->user_id) {
            return true;
        }

        // HR can view if it belongs to their company
        return $user->companyMemberships()
            ->where('company_id', $application->internship->company_id)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Student can create via Apply
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Application $application): bool
    {
        // Student can update (e.g., withdraw) if status is pending
        if ($user->id === $application->user_id && $application->status === 'pending') {
            return true;
        }

        // HR can update if it belongs to their company
        return $user->companyMemberships()
            ->where('company_id', $application->internship->company_id)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Application $application): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Application $application): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Application $application): bool
    {
        return false;
    }
}
