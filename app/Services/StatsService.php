<?php

namespace App\Services;

use App\Models\Squawks\Allocation;
use App\Models\Squawks\AllocationHistory;
use App\Models\User\User;
use App\Models\Version\Version;
use Carbon\Carbon;

class StatsService
{
    /**
     * @return array
     */
    public function getStats() : array
    {
        $latestVersion = Version::orderBy('id', 'desc')->first();
        $data = [
            'latest_plugin_version' => $latestVersion->version,
            'total_users' => User::count(),
            'active_users' => User::where('last_login', '>', Carbon::now()->subMonths(3))->count(),
            'users_today' => User::where('last_login', '>', Carbon::today()->toDateTimeString())->count(),
            'active_users_latest_version' =>
                User::where(
                    [
                        ['last_login', '>', Carbon::now()->subMonths(3)],
                        ['last_version', '=', $latestVersion->id],
                    ]
                )->count(),
            'current_squawks_assigned' => Allocation::count(),
            'squawks_assigned_3_mo' => AllocationHistory::count(),
            'timestamp' => Carbon::now()->toDateTimeString(),
        ];

        return $data;
    }
}
