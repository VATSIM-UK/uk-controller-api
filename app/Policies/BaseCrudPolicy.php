<?php

namespace App\Policies;

use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * A base policy for doing things via Filament.
 */
class BaseCrudPolicy
{
    use ChecksUserRoles;
    use HandlesAuthorization;

    protected $roles = [];

    public function view(): bool
    {
        return true;
    }

    public function viewAny(): bool
    {
        return true;
    }

    public function moveUp(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function moveDown(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function attach(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function detach(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function update(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function create(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function delete(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function restore(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function forceDelete(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function detachAny(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function dissociate(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function dissociateAny(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function replicate(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
    }

    public function restoreAny(User $user): bool
    {
        return $this->userHasRole(
            $user,
            $this->roles
        );
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
