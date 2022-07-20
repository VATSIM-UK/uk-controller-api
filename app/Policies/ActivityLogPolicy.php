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
    use HandlesAuthorization;

    public function view(User $user): bool
    {
        return $this->userCanViewActivity($user);
    }

    public function viewAny(User $user): bool
    {
        return $this->userCanViewActivity($user);
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

    private function userCanViewActivity(User $user): bool
    {
        foreach ($user->roles as $role)
        {
            if ($role->isOneOf([RoleKeys::DIVISION_STAFF_GROUP, RoleKeys::WEB_TEAM])) {
                return true;
            }
        }

        return false;
    }
}
