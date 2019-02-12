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
                'data' => json_encode(
                    [
                        'foo' => 'bar',
                    ]
                ),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'data' => json_encode(
                    [
                        'foo' => 'bar',
                    ]
                ),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ];

        HoldProfile::insert($holds);
    }
}
