<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChannelIslandsDomesticRanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('airfield_pairing_squawk_ranges')->insert(
            [
                [
                    'origin' => 'EGJJ',
                    'destination' => 'EG',
                    'first' => '1201',
                    'last' => '1277',
                    'created_at' => Carbon::now(),
                ],
                [
                    'origin' => 'EGJB',
                    'destination' => 'EG',
                    'first' => '1201',
                    'last' => '1277',
                    'created_at' => Carbon::now(),
                ],
                [
                    'origin' => 'EGJA',
                    'destination' => 'EG',
                    'first' => '1201',
                    'last' => '1277',
                    'created_at' => Carbon::now(),
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('airfield_pairing_squawk_ranges')
            ->whereIn('origin', ['EGJJ', 'EGJB', 'EGJA'])
            ->where('destination', 'EG')
            ->delete();
    }
}
