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
    /**
     * Returns the current holds in a format
     * that may be converted to a JSON array.
     *
     * @return array
     */
    public function getHolds(): array
    {
        $data = Hold::with('restrictions', 'navaid')->get()->toArray();
        foreach ($data as $key => $hold) {
            foreach ($hold['restrictions'] as $restrictionKey => $restriction) {
                $data[$key]['restrictions'][$restrictionKey] =
                    $data[$key]['restrictions'][$restrictionKey]['restriction'];
            }

            $data[$key]['fix'] = $data[$key]['navaid']['identifier'];
            unset($data[$key]['navaid_id'], $data[$key]['navaid']);
        }

        return $data;
    }

    /**
     * Get an array of all the user hold profiles
     *
     * @return array
     */
    public function getUserHoldProfiles(): array
    {
        $profiles = HoldProfile::where('user_id', '=', Auth::user()->id)
            ->get()
            ->toArray();

        foreach ($profiles as $key => $profile) {
            unset($profiles[$key]['hold_profile_hold']);
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
