<?php

use App\Models\Hold\HoldProfileUser;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HoldProfileUserTableSeeder extends Seeder
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
                'name' => 'User Hold Profile 1',
                'user_id' => UserTableSeeder::ACTIVE_USER_CID,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'User Hold Profile 2',
                'user_id' => UserTableSeeder::ACTIVE_USER_CID,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ];

        HoldProfileUser::insert($holds);
    }
}
