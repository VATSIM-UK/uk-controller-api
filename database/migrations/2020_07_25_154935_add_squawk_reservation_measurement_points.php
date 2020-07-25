<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSquawkReservationMeasurementPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('squawk_reservation_measurement_points')
            ->insert(
                [
                    [
                        'latitude' => 53.58694,
                        'longitude' => 1.30083,
                        'description' => 'EGTT - UPTON',
                    ],
                    [
                        'latitude' => 52.14194,
                        'longitude' => -2.06055,
                        'description' => 'EGTT - LUXTO',
                    ],
                    [
                        'latitude' => 50.675,
                        'longitude' => -1.85,
                        'description' => 'EGTT - KAPEX',
                    ],
                    [
                        'latitude' => 58.96833,
                        'longitude' => -3.87277,
                        'description' => 'EGPX - SOXON',
                    ],
                    [
                        'latitude' => 55.79944,
                        'longitude' => -5.33333,
                        'description' => 'EGPX - TABIT',
                    ],
                    [
                        'latitude' => 55.465,
                        'longitude' => 0.2480,
                        'description' => 'EGPX - GIVEM',
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
        DB::table('squawk_reservation_measurement_points')->delete();
    }
}
