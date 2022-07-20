<?php

namespace App\Policies;

use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * A base policy for doing things via Filament.
 */
class DefaultFilamentPolicy
{
    use HandlesAuthorization;

    public function view(): bool
    {
        return true;
    }

    public function viewAny(): bool
    {
        return true;
    }

    public function update(User $user): bool
    {
        return $this->userHasEditingRole($user);
    }

    public function create(User $user): bool
    {
        return $this->userHasEditingRole($user);
    }

    public function delete(User $user): bool
    {
        return $this->userHasEditingRole($user);
    }

    public function restore(User $user): bool
    {
        return $this->userHasEditingRole($user);
    }

    public function forceDelete(User $user): bool
    {
        return $this->userHasEditingRole($user);
    }

    private function userHasEditingRole(User $user): bool
    {
        foreach ($user->roles as $role)
        {
            if ($role->isOneOf([RoleKeys::DIVISION_STAFF_GROUP, RoleKeys::WEB_TEAM, RoleKeys::OPERATIONS_TEAM])) {
                return true;
            }
        }

        return false;
    }
}
