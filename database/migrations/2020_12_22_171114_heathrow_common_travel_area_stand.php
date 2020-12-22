<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HeathrowCommonTravelAreaStand extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $speedbird = DB::table('airlines')->where('icao_code', 'BAW')->first()->id;
        $stand = DB::table('stands')
            ->where('identifier', '523')
            ->where('airfield_id', DB::table('airfield')->where('code', 'EGLL')->first()->id)
            ->first()
            ->id;

        // Delete the current rows, we can make them better for the CTA.
        DB::table('airline_stand')
            ->where('stand_id', $stand)
            ->where('airline_id', $speedbird)
            ->delete();

        // Add new stand details
        DB::table('airline_stand')
            ->insert(
                [
                    [
                        'airline_id' => $speedbird,
                        'stand_id' => $stand,
                        'destination' => 'EI', // Eire
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'airline_id' => $speedbird,
                        'stand_id' => $stand,
                        'destination' => 'EGJ', // Channel islands
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'airline_id' => $speedbird,
                        'stand_id' => $stand,
                        'destination' => 'EGNS', // Isle of Mann
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
        //
    }
}
