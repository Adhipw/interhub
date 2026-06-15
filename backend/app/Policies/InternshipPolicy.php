<?php

namespace App\Policies;

use App\Models\Internship;
use App\Models\User;

class InternshipPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Internship $internship): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['hr', 'admin', 'super_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Internship $internship): bool
    {
        return $user->companyMemberships()
            ->where('company_id', $internship->company_id)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Internship $internship): bool
    {
        return $user->companyMemberships()
            ->where('company_id', $internship->company_id)
            ->where('is_active', true)
            ->whereIn('role', ['owner', 'hr'])
            ->exists();
    }
}
