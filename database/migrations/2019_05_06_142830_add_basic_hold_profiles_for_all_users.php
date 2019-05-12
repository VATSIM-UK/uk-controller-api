<?php

use App\Models\Hold\HoldProfile;
use App\Models\Hold\HoldProfileHold;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBasicHoldProfilesForAllUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = User::all();
        $users->each(function (User $user) {
            // Gatwick
            $this->createHoldProfile(
                $user->id,
                'Gatwick APC',
                [1, 2]
            );

            // Manchester
            $this->createHoldProfile(
                $user->id,
                'Manchester APC Combined',
                [3, 4, 5]
            );

            // Heathrow
            $this->createHoldProfile(
                $user->id,
                'Heathrow APC Combined',
                [6, 7, 8, 9]
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $profiles = HoldProfile::all();
        $profiles->each(function (HoldProfile $profile) {
            $profile->delete();
        });
    }

    /**
     * @param int $userId
     * @param string $profileName
     * @param array $profileHolds
     */
    private function createHoldProfile(int $userId, string $profileName, array $profileHolds)
    {
        $profile = HoldProfile::create(
            [
                'user_id' => $userId,
                'name' => $profileName,
                'created_at' => Carbon::now(),
            ]
        );

        foreach ($profileHolds as $hold) {
            HoldProfileHold::create(
                [
                    'hold_profile_id' => $profile->id,
                    'hold_id' => $hold
                ]
            );
        }
    }
}
