<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SetNavaidCoordinates extends Migration
{
    const NAVAIDS = [
        'WILLO' => [
            'N050.59.06.000',
            'W000.11.30.000',
        ],
        'TIMBA' => [
            'N050.56.44.000',
            'E000.15.42.000',
        ],
        'DAYNE' => [
            'N053.14.19.000',
            'W002.01.45.000',
        ],
        'ROSUN' => [
            'N053.40.08.000',
            'W002.20.57.000',
        ],
        'BIG' => [
            'N051.19.51.150',
            'E000.02.05.320',
        ],
        'OCK' => [
            'N051.18.18.000',
            'W000.26.50.000',
        ],
        'BNN' => [
            'N051.43.34.000',
            'W000.32.59.000',
        ],
        'LAM' => [
            'N051.38.46.000',
            'E000.09.06.000',
        ],
        'LOREL' => [
            'N052.00.50.000',
            'W000.03.10.000',
        ],
        'ABBOT' => [
            'N052.00.58.000',
            'E000.35.58.000',
        ],
        'BRI' => [
            'N051.22.53.190',
            'W002.43.03.160',
        ],
        'CDF' => [
            'N051.23.36.160',
            'W003.20.16.470',
        ],
        'TIPOD' => [
            'N053.26.08.000',
            'W003.17.05.000',
        ],
        'OCK' => [
            'N051.18.18.000',
            'W000.26.50.000',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            foreach (self::NAVAIDS as $navaid => $coordinates) {
                DB::table('navaids')->where('identifier', $navaid)
                    ->update(['latitude' => $coordinates[0], 'longitude' => $coordinates[1]]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('navaids')->update(['latitude' => null, 'longitude' => null]);
    }
}
