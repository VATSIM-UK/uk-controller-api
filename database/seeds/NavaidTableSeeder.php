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
                    'identifier' => 'WILLO',
                    'created_at' => Carbon::now(),
                ],
                [
                    'id' => 2,
                    'identifier' => 'TIMBA',
                    'created_at' => Carbon::now(),
                ],
                [
                    'id' => 3,
                    'identifier' => 'MAY',
                    'created_at' => Carbon::now(),
                ],
            ]
        );
    }
}
