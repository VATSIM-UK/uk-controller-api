<?php

use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Database\Seeder;

class DevUserSeeder extends Seeder
{
    // .docker/web/sso/index.php
    const DEV_USER_CID = 1234;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create or retrieve the dev test user
        $user = User::firstOrCreate(
            ['id' => self::DEV_USER_CID],
            [
                'first_name' => 'Dev',
                'last_name' => 'User',
                'status' => 1,
            ]
        );

        // Assign all roles to the dev user
        $roleIds = Role::all()->pluck('id')->toArray();
        $user->roles()->syncWithoutDetaching($roleIds);
    }
}
