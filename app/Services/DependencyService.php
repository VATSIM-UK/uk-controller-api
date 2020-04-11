<?php

namespace App\Services;

use App\Models\Dependency\Dependency;
use App\Models\User\User;
use Carbon\Carbon;
use LogicException;

class DependencyService
{
    public static function touchDependency(string $key)
    {
        $dependency = Dependency::where('key', $key)->first();
        $dependency && $dependency->touch();
    }

    public static function touchUserDependency(string $key, User $user)
    {
        $dependency = Dependency::where('key', $key)->first();

        if (!$dependency->per_user) {
            throw new LogicException(sprintf('Dependency %s is not a per-user dependency', $key));
        }

        $user->dependencies()->updateExistingPivot($dependency->id, ['updated_at' => Carbon::now()]);
    }
}
