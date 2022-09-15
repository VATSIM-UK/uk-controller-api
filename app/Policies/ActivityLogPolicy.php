<?php

namespace App\Policies;

use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * A base policy for doing things via Filament.
 */
class ActivityLogPolicy
{
    use ChecksUserRoles;
    use HandlesAuthorization;

    private const ROLES = [RoleKeys::DIVISION_STAFF_GROUP, RoleKeys::WEB_TEAM];

    public function view(User $user): bool
    {
        return $this->userHasRole(
            $user,
            self::ROLES
        );
    }

    public function viewAny(User $user): bool
    {
        return $this->userHasRole(
            $user,
            self::ROLES
        );
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

    public function deleteAny(): bool
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
}
