<?php

namespace App\Policies;

use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PluginEditableDataPolicy
{
    use HandlesAuthorization;

    public function view(): bool
    {
        return true;
    }

    public function viewAny(): bool
    {
        return true;
    }

    public function moveUp(): bool
    {
        return true;
    }

    public function moveDown(): bool
    {
        return true;
    }

    public function attach(): bool
    {
        return true;
    }

    public function detach(): bool
    {
        return true;
    }

    public function update(): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function delete(): bool
    {
        return true;
    }

    public function restore(): bool
    {
        return true;
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
        return true;
    }

    public function dissociateAny(): bool
    {
        return false;
    }

    public function replicate(): bool
    {
        return true;
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
