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
                    'latitude' => 'abc',
                    'longitude' => 'def',
                ],
                [
                    'airfield_id' => 1,
                    'identifier' => '251',
                    'latitude' => 'asd',
                    'longitude' => 'hsd',
                ],
                [
                    'airfield_id' => 2,
                    'identifier' => '32',
                    'latitude' => 'fhg',
                    'longitude' => 'sda',
                ],
            ]
        );
    }
}
