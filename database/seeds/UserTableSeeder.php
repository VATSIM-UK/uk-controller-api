<?php

use App\Models\User\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    // CID constants so that they can be used around
    const ADMIN_USER_CID = 1;
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
                'first_name' => 'User',
                'last_name' => 'Admin',
                'status' => 1,
            ]
        );

        // Regular users
        User::create(
            [
                'id' => self::ACTIVE_USER_CID,
                'first_name' => 'User',
                'last_name' => 'Active',
                'status' => 1,
            ]
        );
        User::create(
            [
                'id' => self::BANNED_USER_CID,
                'first_name' => 'User',
                'last_name' => 'Banned',
                'status' => 2,
            ]
        );

        User::create(
            [
                'id' => self::DISABLED_USER_CID,
                'first_name' => 'User',
                'last_name' => 'Account Disabled',
                'status' => 3,
            ]
        );
    }
}
