<?php

namespace App\Policies;

use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use ChecksUserRoles;
    use HandlesAuthorization;

    private const ROLES = [RoleKeys::DIVISION_STAFF_GROUP, RoleKeys::WEB_TEAM];

    public function viewAny(User $user)
    {
        return $this->userHasRole(
            $user,
            self::ROLES
        );
    }

    public function view(User $user)
    {
        return $this->userHasRole(
            $user,
            self::ROLES
        );
    }

    public function create()
    {
        return false;
    }

    public function update(User $user, User $model)
    {
        return $this->userHasRole(
            $user,
            self::ROLES
        ) && $user->id !== $model->id;
    }

    public function attach(User $user, User $model)
    {
        return $this->userHasRole(
                $user,
                self::ROLES
            ) && $user->id !== $model->id;
    }

    public function detach(User $user, User $model)
    {
        return $this->userHasRole(
                $user,
                self::ROLES
            ) && $user->id !== $model->id;
    }

    public function delete()
    {
        return false;
    }

    public function restore()
    {
        return false;
    }

    public function forceDelete()
    {
        return false;
    }
}
