<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignedHoldsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('assigned_holds')->insert(
            [
                [
                    'callsign' => 'BAW123',
                    'navaid_id' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'BAW456',
                    'navaid_id' => 2,
                    'created_at' => Carbon::now(),
                ],
            ]
        );
    }
}
