<?php

namespace App\Policies;

use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Plugin version filament policy.
 */
class PluginVersionPolicy
{
    use ChecksUserRoles;
    use HandlesAuthorization;

    private const DELETING_ROLES = [RoleKeys::WEB_TEAM];

    public function view(): bool
    {
        return true;
    }

    public function viewAny(): bool
    {
        return true;
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

    public function delete(User $user): bool
    {
        return $this->userHasRole(
            $user,
            self::DELETING_ROLES
        );
    }

    public function restore(User $user): bool
    {
        return $this->userHasRole(
            $user,
            self::DELETING_ROLES
        );
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
