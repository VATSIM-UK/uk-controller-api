<?php

use App\Models\User\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create(
            [
                'user_id' => UserTableSeeder::ACTIVE_USER_CID,
                'email' => 'ukcp@vatsim.uk',
                'password' => Hash::make('letmein'),
            ]
        );
    }
}
