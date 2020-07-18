<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAirfieldPairingSquawkRanges extends Migration
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
                    'destination' => 'EGJB',
                    'first' => '3710',
                    'last' => '3727',
                    'created_at' => Carbon::now(),
                ],
                [
                    'origin' => 'EGJJ',
                    'destination' => 'EGJA',
                    'first' => '3710',
                    'last' => '3727',
                    'created_at' => Carbon::now(),
                ],
                [
                    'origin' => 'EGJB',
                    'destination' => 'EGJA',
                    'first' => '3730',
                    'last' => '3747',
                    'created_at' => Carbon::now(),
                ],
                [
                    'origin' => 'EGJB',
                    'destination' => 'EGJJ',
                    'first' => '3730',
                    'last' => '3747',
                    'created_at' => Carbon::now(),
                ],
                [
                    'origin' => 'EGJA',
                    'destination' => 'EGJB',
                    'first' => '3730',
                    'last' => '3747',
                    'created_at' => Carbon::now(),
                ],
                [
                    'origin' => 'EGJA',
                    'destination' => 'EGJJ',
                    'first' => '3730',
                    'last' => '3747',
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
        DB::table('airfield_pairing_squawk_ranges')->truncate();
    }
}
