<?php

namespace App\Services;

use App\Models\Hold\Hold;
use App\Models\Hold\HoldProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HoldService
{
    const CACHE_KEY = 'hold-data';

    /**
     * Returns the current holds in a format
     * that may be converted to a JSON array.
     *
     * @return array
     */
    public function getHolds(): array
    {
        if (Cache::has(self::CACHE_KEY)) {
            return Cache::get(self::CACHE_KEY);
        }

        $data = Hold::with('restrictions')->get()->toArray();
        Cache::forever(self::CACHE_KEY, $data);
        return $data;
    }

    /**
     * Clear the hold cache
     */
    public function clearCache()
    {
        if (!Cache::forget(self::CACHE_KEY)) {
            Log::warning('Hold cache clear failed');
            return;
        }

        Log::info('Hold cache cleared');
    }

    /**
     * Get an array of all the generic hold profiles
     *
     * @return array
     */
    public function getGenericHoldProfiles() : array
    {
        $profiles = HoldProfile::with('holds')
            ->whereNull('user_id')
            ->get()
            ->toArray();

        foreach ($profiles as $key => $profile) {
            $profiles[$key]['holds'] = array_column($profile['holds'], 'id');
        }

        return $profiles;
    }

    /**
     * Get an array of all the user hold profiles
     *
     * @return array
     */
    public function getUserHoldProfiles() : array
    {
        $profiles = HoldProfile::with('holds')
            ->where('user_id', '=', Auth::user()->id)
            ->get()
            ->toArray();

        foreach ($profiles as $key => $profile) {
            $profiles[$key]['holds'] = array_column($profile['holds'], 'id');
        }

        return $profiles;
    }

    /**
     * Get an array of the users hole profiles
     * and the generic ones
     *
     * @return array
     */
    public function getUserAndGenericHoldProfiles() : array
    {
        $profiles = HoldProfile::with('holds')
            ->where('user_id', '=', Auth::user()->id)
            ->orWhereNull('user_id')
            ->get()
            ->toArray();

        foreach ($profiles as $key => $profile) {
            $profiles[$key]['holds'] = array_column($profile['holds'], 'id');
        }

        return $profiles;
    }
}
