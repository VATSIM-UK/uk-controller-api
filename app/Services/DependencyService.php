<?php

namespace App\Services;

use App\Models\Database\DatabaseTable;
use App\Models\Dependency\Dependency;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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
            function () use ($dependency) {
                $response = app()->call("App\\Http\\Controllers\\{$dependency->action}");
                if (!$response instanceof JsonResponse) {
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

    private static function removeDependencyFromCache(Dependency $dependency): void
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
        if (($user = Auth::user()) === null) {
            throw new LogicException('User dependencies can only be updated by logged in users');
        }

        if (!$dependency->per_user) {
            throw new LogicException(sprintf('Dependency %s is not a per-user dependency', $dependency->key));
        }

        $user->dependencies()->updateExistingPivot($dependency->id, ['updated_at' => Carbon::now()]);
        self::removeDependencyFromCache($dependency);
    }

    public static function deleteDependency(string $key)
    {
        DB::table('dependencies')
            ->where('key', $key)
            ->delete();
        Cache::forget($key);
    }

    public static function createDependency(
        string $key,
        string $action,
        bool   $perUser,
        string $filename,
        array  $concernedTables
    ): void {
        DB::transaction(function () use ($key, $action, $perUser, $filename, $concernedTables) {
            Dependency::create(
                [
                    'key' => $key,
                    'action' => $action,
                    'local_file' => $filename,
                    'per_user' => $perUser,
                ]
            );

            self::setConcernedTablesForDependency($key, $concernedTables);
        });
    }

    public static function setConcernedTablesForDependency(string $dependencyKey, array $concernedTables): void
    {
        foreach ($concernedTables as $concernedTable) {
            if (!Schema::hasTable($concernedTable)) {
                throw new InvalidArgumentException(
                    sprintf('Database table %s does not exist for dependency', $concernedTable)
                );
            }
        }

        $allTables = DatabaseTable::all();
        Dependency::where('key', $dependencyKey)->firstOrFail()->databaseTables()->sync(
            $allTables->filter(function (DatabaseTable $table) use ($concernedTables) {
                return array_search($table->name, $concernedTables) !== false;
            })
                ->pluck('id')
        );
    }

    /**
     * Check which tables have been recently updated and touch
     * their related dependencies.
     */
    public static function checkForDependencyUpdates(): void
    {
        $currentTables = self::getCurrentTableStatistics();
        $liveStats = self::getLiveTableStatistics($currentTables);

        foreach ($currentTables as $table) {
            if (self::tableRequiresDependencyUpdate($table, $liveStats)) {
                $table->dependencies->each(function (Dependency $dependency) {
                    self::touchGlobalDependency($dependency);
                });

                $liveTime = $liveStats->get($table->name);
                $table->updated_at = $liveTime === null ? Carbon::now() : $liveTime;
                $table->save();
            }
        }
    }

    private static function tableRequiresDependencyUpdate(
        DatabaseTable $table,
        Collection    $liveStats
    ): bool {
        return $table->updated_at === null ||
            (
                $liveStats->get($table->name) !== null &&
                $table->updated_at < $liveStats->get($table->name)
            );
    }

    private static function getCurrentTableStatistics(): Collection
    {
        return DatabaseTable::with('dependencies')
            ->whereHas('dependencies')
            ->get()
            ->mapWithKeys(function (DatabaseTable $table) {
                return [
                    $table->name => $table,
                ];
            });
    }

    private static function getLiveTableStatistics(Collection $tables): Collection
    {
        DB::statement('ANALYZE TABLE ' . $tables->implode('name', ','));
        return DB::table('information_schema.TABLES')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->whereIn('TABLE_NAME', $tables->pluck('name')->unique()->toArray())
            ->get()
            ->mapWithKeys(function (object $table) {
                return [
                    $table->TABLE_NAME => $table->UPDATE_TIME ? Carbon::parse($table->UPDATE_TIME) : null,
                ];
            });
    }
}
