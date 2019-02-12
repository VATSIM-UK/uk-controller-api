<?php

use App\Models\Hold\HoldProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HoldProfileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $holds = [
            [
                'name' => 'Generic Hold Profile',
                'user_id' => null,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'User Hold Profile',
                'user_id' => UserTableSeeder::ACTIVE_USER_CID,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ];

        HoldProfile::insert($holds);
    }
}
