<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can moderate the target user.
     */
    public function moderate(User $user, User $target): Response
    {
        // Super Admin can moderate anyone
        if ($user->hasRole('super_admin')) {
            return Response::allow();
        }

        // Admin can moderate anyone except Super Admin
        if ($user->hasRole('admin')) {
            return $target->hasRole('super_admin')
                ? Response::deny('Anda tidak diizinkan untuk memoderasi Super Admin.')
                : Response::allow();
        }

        return Response::deny('Anda tidak memiliki izin moderasi.');
    }

    /**
     * Determine whether the user can delete the target user.
     */
    public function delete(User $user, User $target): Response
    {
        return $this->moderate($user, $target);
    }
}
