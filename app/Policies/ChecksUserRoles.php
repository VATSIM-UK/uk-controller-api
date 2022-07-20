<?php

namespace App\Policies;

use App\Models\User\User;

trait ChecksUserRoles
{
    private function userHasRole(User $user, array $roles): bool
    {
        foreach ($user->roles as $role)
        {
            if ($role->isOneOf($roles)) {
                return true;
            }
        }

        return false;
    }
}
