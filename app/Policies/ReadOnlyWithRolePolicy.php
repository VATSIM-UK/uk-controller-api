<?php

namespace App\Policies;

use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy that allows read only access only to users with a role.
 */
class ReadOnlyWithRolePolicy
{
    use ChecksUserRoles;
    use HandlesAuthorization;
    use RejectsNonReadOnlyActions;

    public function view(?User $user): bool
    {
        return $this->userHasAnyRole($user);
    }

    public function viewAny(?User $user): bool
    {
        return $this->userHasAnyRole($user);
    }
}
