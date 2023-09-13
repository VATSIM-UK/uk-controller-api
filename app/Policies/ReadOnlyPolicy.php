<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy that allows read only access only.
 */
class ReadOnlyPolicy
{
    use HandlesAuthorization;
    use RejectsNonReadOnlyActions;

    public function view(): bool
    {
        return true;
    }

    public function viewAny(): bool
    {
        return true;
    }
}
