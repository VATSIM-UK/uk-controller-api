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
                    'latitude' => 51.47436111, // 501 at LL
                    'longitude' => -0.48953611,
                    'aerodrome_reference_code' => 'C',
                ],
                [
                    'airfield_id' => 1,
                    'identifier' => '251',
                    'latitude' => 51.47187222, // 512 at LL
                    'longitude' => -0.48601389,
                    'aerodrome_reference_code' => 'C',
                ],
                [
                    'airfield_id' => 2,
                    'identifier' => '32',
                    'latitude' => 52.44979111, // 20 at BB
                    'longitude' => -1.73186694,
                    'aerodrome_reference_code' => 'C',
                ],
            ]
        );
    }
}
