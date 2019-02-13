<?php

namespace App\Services;

use App\Models\Hold\Hold;
use App\Models\Hold\HoldProfile;
use App\Models\Hold\HoldProfileHold;
use Carbon\Carbon;
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
    public function getGenericHoldProfiles(): array
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
    public function getUserHoldProfiles(): array
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
    public function getUserAndGenericHoldProfiles(): array
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

    /**
     * Deletes the specified user hold profile and
     * all the holds associated with it
     *
     * @param int $holdProfileId
     */
    public function deleteUserHoldProfile(int $holdProfileId)
    {
        HoldProfile::where(
            [
                ['user_id', Auth::user()->id],
                ['id', $holdProfileId],
            ]
        )->delete();
    }

    /**
     * Create a user hold profile with associated holds
     *
     * @param string $profileName The name of the profile
     * @param array $holds Array of hold ids
     * @return HoldProfile
     */
    public function createUserHoldProfile(string $profileName, array $holds): HoldProfile
    {
        $holdProfile = HoldProfile::create(
            [
                'user_id' => Auth::user()->id,
                'name' => $profileName,
                'created_at' => Carbon::now()->toDateTimeString(),
            ]
        );

        $holds = array_map(function ($holdId) use ($holdProfile) {
            return [
                'hold_profile_id' => $holdProfile->id,
                'hold_id' => $holdId,
            ];
        }, $holds);

        HoldProfileHold::insert($holds);
        return $holdProfile;
    }

    /**
     * Updates the specified hold profile and its associated holds
     *
     * @param int $holdProfileId The id of the profile to update
     * @param string $profileName The name of the profile
     * @param array $holds Array of hold ids
     */
    public function updateUserHoldProfile(int $holdProfileId, string $profileName, array $holds)
    {
        $holdProfile = HoldProfile::where(
            [
                ['id', $holdProfileId],
                ['user_id', Auth::user()->id],
            ]
        )->firstOrFail();

        $holdProfile->update(
            [
                'name' => $profileName,
            ]
        );

        HoldProfileHold::where('hold_profile_id', $holdProfileId)->delete();
        $holds = array_map(function ($holdId) use ($holdProfile) {
            return [
                'hold_profile_id' => $holdProfile->id,
                'hold_id' => $holdId,
            ];
        }, $holds);

        HoldProfileHold::insert($holds);
    }
}
