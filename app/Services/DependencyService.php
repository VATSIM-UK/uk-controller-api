<?php

namespace App\Services;

use App\Models\Dependency\Dependency;
use App\Models\User\User;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use LogicException;

class DependencyService
{
    /**
     * Given a dependency, fetch the cached version or cache if it we need to.
     */
    public static function fetchDependencyDataById(int $id): array
    {
        return self::fetchDependencyData(Dependency::findOrFail($id));
    }

    private static function fetchDependencyData(Dependency $dependency): array
    {
        return Cache::rememberForever(
            self::getDependencyCacheKey($dependency),
            function () use ($dependency)
            {
                $response = app()->call(sprintf('App\\Http\\Controllers\\%s', $dependency->action));
                if (!$response instanceof JsonResponse)
                {
                    throw new InvalidArgumentException('Returned dependency was not JSON');
                }

                return $response->getData(true);
            }
        );
    }

    private static function getDependencyCacheKey(Dependency $dependency): string
    {
        return $dependency->per_user
            ? sprintf('DEPENDENCY_%d_CACHE_USER_%d', $dependency->id, Auth::id())
            : sprintf('DEPENDENCY_%d_CACHE', $dependency->id);
    }

    public static function touchDependencyByKey(string $key, ?User $user = null): void
    {
        $dependency = Dependency::where('key', $key)->first();

        if (!$dependency) {
            Log::error(sprintf('Dependency %s not found to update', $key));
            return;
        }

        if ($dependency->per_user && $user === null) {
            Log::error(sprintf('Dependency %s is per user but user was not specified', $key));
            return;
        }

        if ($dependency->per_user) {
            self::touchUserDependency($dependency);
        } else {
            self::touchGlobalDependency($dependency);
        }
    }

    private static function removeDependencyFromCache(Dependency $dependency)
    {
        Cache::forget(self::getDependencyCacheKey($dependency));
    }

    /**
     * Update the time on the dependency and re-cache it
     * @param Dependency $dependency
     */
    public static function touchGlobalDependency(Dependency $dependency): void
    {
        $dependency->touch();
        self::removeDependencyFromCache($dependency);
    }

    public static function touchUserDependency(Dependency $dependency): void
    {
        if (($user = Auth::user()) === null)
        {
            throw new LogicException('User dependencies can only be updated by logged in users');
        }

        if (!$dependency->per_user) {
            throw new LogicException(sprintf('Dependency %s is not a per-user dependency', $dependency->key));
        }

        $user->dependencies()->updateExistingPivot($dependency->id, ['updated_at' => Carbon::now()]);
        self::removeDependencyFromCache($dependency);
    }
}
