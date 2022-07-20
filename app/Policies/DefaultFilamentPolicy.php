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
    use ChecksUserRoles;
    use HandlesAuthorization;

    private const EDITING_ROLES = [RoleKeys::DIVISION_STAFF_GROUP, RoleKeys::WEB_TEAM, RoleKeys::OPERATIONS_TEAM];

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
        return $this->userHasRole(
            $user,
            self::EDITING_ROLES
        );
    }

    public function create(User $user): bool
    {
        return $this->userHasRole(
            $user,
            self::EDITING_ROLES
        );
    }

    public function delete(User $user): bool
    {
        return $this->userHasRole(
            $user,
            self::EDITING_ROLES
        );
    }

    public function restore(User $user): bool
    {
        return $this->userHasRole(
            $user,
            self::EDITING_ROLES
        );
    }

    public function forceDelete(User $user): bool
    {
        return $this->userHasRole(
            $user,
            self::EDITING_ROLES
        );
    }
}
