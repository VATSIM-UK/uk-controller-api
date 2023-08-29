<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User\User;

/**
 * Policy that allows read only access only to users with a role.
 */
class ReadOnlyWithRolePolicy extends ReadOnlyPolicy
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
}
