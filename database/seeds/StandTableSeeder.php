<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stands')->insert(
            [
                [
                    'airfield_id' => 1,
                    'identifier' => '1L',
                    'latitude' => 54.65875500,
                    'longitude' => -6.22258694,
                    'wake_category_id' => 5,
                ],
                [
                    'airfield_id' => 1,
                    'identifier' => '251',
                    'latitude' => 54.65883639,
                    'longitude' => -6.22198972,
                    'wake_category_id' => 5,
                ],
                [
                    'airfield_id' => 2,
                    'identifier' => '32',
                    'latitude' => 52.44979111,
                    'longitude' => -1.73186694,
                    'wake_category_id' => 5,
                ],
            ]
        );
    }
}
