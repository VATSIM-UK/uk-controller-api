<?php

namespace App\Policies;

use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy that allows read only access only.
 */
class ReadOnlyPolicy
{
    use HandlesAuthorization;
    use ReadOnlyPolicyMethods;

    public function view(): bool
    {
        return true;
    }

    public function viewAny(): bool
    {
        return true;
    }
}
