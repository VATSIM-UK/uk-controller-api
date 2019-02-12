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
                'data' => json_encode(
                    [
                        'foo' => 'bar',
                    ]
                ),
                'user_id' => UserTableSeeder::ACTIVE_USER_CID,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'data' => json_encode(
                    [
                        'foo' => 'bar',
                    ]
                ),
                'user_id' => UserTableSeeder::ACTIVE_USER_CID,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ];

        HoldProfileUser::insert($holds);
    }
}
