<?php

namespace App\Services;

use App\Models\Dependency\Dependency;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use LogicException;

class DependencyService
{
    public static function touchDependencyByKey(string $key, ?User $user = null): void
    {
        $dependency = Dependency::where('key', $key)->first();

        if (!$dependency) {
            Log::error(sprintf('Dependency %s not found to update', $key));
            return;
        }

        if ($dependency->per_user && $user === null) {
            Log::error(sprintf('Dependency %s is per user but user was not specifieid', $key));
            return;
        }

        if ($dependency->per_user) {
            self::touchUserDependency($dependency, $user);
        } else {
            self::touchGlobalDependency($dependency);
        }
    }

    public static function touchGlobalDependency(Dependency $dependency): void
    {
        $dependency->touch();
    }

    public static function touchUserDependency(Dependency $dependency, User $user): void
    {
        if (!$dependency->per_user) {
            throw new LogicException(sprintf('Dependency %s is not a per-user dependency', $dependency->key));
        }

        $user->dependencies()->updateExistingPivot($dependency->id, ['updated_at' => Carbon::now()]);
    }
}
