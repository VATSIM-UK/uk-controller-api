<?php

namespace App\Policies;

use App\Models\User\User;

trait ChecksUserRoles
{
    private function userHasAnyRole(User $user): bool
    {
        return $user->roles()->count() > 0;
    }

    private function userHasRole(User $user, array $roles): bool
    {
        return self::checkUserHasRole($user, $roles);
    }

    private static function checkUserHasRole(User $user, array $roles): bool
    {
        foreach ($user->roles as $role) {
            if ($role->isOneOf($roles)) {
                return true;
            }
        }

        return false;
    }
}
