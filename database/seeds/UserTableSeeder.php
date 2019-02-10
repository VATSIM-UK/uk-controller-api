<?php

use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    // CID constants so that they can be used around
    const ADMIN_USER_CID = 0;
    const ACTIVE_USER_CID = 1203533;
    const BANNED_USER_CID = 1203534;
    const DISABLED_USER_CID = 1203535;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User admins
        User::create(
            [
                'id' => self::ADMIN_USER_CID,
                'status' => 1,
            ]
        );

        // Regular users
        User::create(
            [
                'id' => self::ACTIVE_USER_CID,
                'status' => 1,
                'last_Login' => Carbon::now(),
                'last_version' => 3,
            ]
        );
        User::create(
            [
                'id' => self::BANNED_USER_CID,
                'status' => 2,
                'last_Login' => Carbon::now()->subDays(1),
                'last_version' => 1,
            ]
        );

        User::create(
            [
                'id' => self::DISABLED_USER_CID,
                'status' => 3,
            ]
        );
    }
}
