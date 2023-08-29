<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User\User;

/**
 * Policy that allows read only access only to users with a role.
 */
class ReadOnlyWithRolePolicy
{
    use HandlesAuthorization;
    use ChecksUserRoles;

    public function view(?User $user): bool
    {
        return $this->userHasAnyRole($user);
    }

    public function viewAny(?User $user): bool
    {
        return $this->userHasAnyRole($user);
    }

    public function attach(): bool
    {
        return false;
    }

    public function detach(): bool
    {
        return false;
    }

    public function update(): bool
    {
        return false;
    }

    public function create(): bool
    {
        return false;
    }

    public function delete(): bool
    {
        return false;
    }

    public function restore(): bool
    {
        return false;
    }

    public function forceDelete(): bool
    {
        return false;
    }

    public function detachAny(): bool
    {
        return false;
    }

    public function dissociate(): bool
    {
        return false;
    }

    public function dissociateAny(): bool
    {
        return false;
    }

    public function replicate(): bool
    {
        return false;
    }

    public function restoreAny(): bool
    {
        return false;
    }

    public function deleteAny(): bool
    {
        return false;
    }

    public function forceDeleteAny(): bool
    {
        return false;
    }
}
