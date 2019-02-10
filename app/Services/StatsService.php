<?php

namespace App\Services;

use App\Models\Squawks\Allocation;
use App\Models\Squawks\AllocationHistory;
use App\Models\User\User;
use App\Models\Version\Version;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Class StatsService
 *
 * For producing and caching stats about plugin usage
 */
class StatsService
{
    // The key against which to cache stats
    const STATS_CACHE_KEY = 'usageStats';

    // The time to cache stats for in minutes
    const STATS_CACHE_TIME = 10;

    /**
     * @return array
     */
    public function getStats(): array
    {
        if (Cache::has(self::STATS_CACHE_KEY)) {
            return Cache::get(self::STATS_CACHE_KEY);
        }

        $latestVersion = Version::orderBy('id', 'desc')->first();
        $data = [
            'latest_plugin_version' => $latestVersion->version,
            'total_users' => User::where('id', '>', UserService::MINIMUM_VATSIM_CID)->count(),
            'active_users' => User::where(
                [
                    ['last_login', '>', Carbon::now()->subMonths(3)],
                    ['id', '>', UserService::MINIMUM_VATSIM_CID],
                ])->count(),
            'users_today' => User::where(
                [
                    ['last_login', '>', Carbon::today()->toDateTimeString()],
                    ['id', '>', UserService::MINIMUM_VATSIM_CID],
                ]
            )->count(),
            'active_users_latest_version' =>
                User::where(
                    [
                        ['last_login', '>', Carbon::now()->subMonths(3)],
                        ['last_version', '=', $latestVersion->id],
                        ['id', '>', UserService::MINIMUM_VATSIM_CID],
                    ]
                )->count(),
            'current_squawks_assigned' => Allocation::count(),
            'squawks_assigned_3_mo' => AllocationHistory::count(),
            'timestamp' => Carbon::now()->toDateTimeString(),
        ];

        Cache::add(self::STATS_CACHE_KEY, $data, self::STATS_CACHE_TIME);
        return $data;
    }
}
