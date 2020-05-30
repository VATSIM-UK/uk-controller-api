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
                    'latitude' => 'abc',
                    'longitude' => 'def',
                    'identifier' => 'WILLO',
                    'created_at' => Carbon::now(),
                ],
                [
                    'id' => 2,
                    'latitude' => 'abc',
                    'longitude' => 'def',
                    'identifier' => 'TIMBA',
                    'created_at' => Carbon::now(),
                ],
                [
                    'id' => 3,
                    'latitude' => 'abc',
                    'longitude' => 'def',
                    'identifier' => 'MAY',
                    'created_at' => Carbon::now(),
                ],
            ]
        );
    }
}
