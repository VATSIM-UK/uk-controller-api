<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavaidTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('navaids')->insert(
            [
                [
                    'id' => 1,
                    'latitude' => 50.9850000,
                    'longitude' => -0.1916667,
                    'identifier' => 'WILLO',
                    'created_at' => Carbon::now(),
                ],
                [
                    'id' => 2,
                    'latitude' => 50.9455556,
                    'longitude' => 0.2616667,
                    'identifier' => 'TIMBA',
                    'created_at' => Carbon::now(),
                ],
                [
                    'id' => 3,
                    'latitude' => 51.017200,
                    'longitude' => 0.116111,
                    'identifier' => 'MAY',
                    'created_at' => Carbon::now(),
                ],
            ]
        );
    }
}
