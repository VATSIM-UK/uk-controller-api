<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddGroundStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('ground_statuses')->insert(
            [
                [
                    'display_string' => 'CLR',
                    'description' => 'Clearance Requested',
                    'created_at' => Carbon::now(),
                ],
                [
                    'display_string' => 'RSTART',
                    'description' => 'Startup Requested',
                    'created_at' => Carbon::now(),
                ],
                [
                    'display_string' => 'START',
                    'description' => 'Startup',
                    'created_at' => Carbon::now(),
                ],
                [
                    'display_string' => 'RPUSH',
                    'description' => 'Push and Start Requested',
                    'created_at' => Carbon::now(),
                ],
                [
                    'display_string' => 'PUSH',
                    'description' => 'Push and Start',
                    'created_at' => Carbon::now(),
                ],
                [
                    'display_string' => 'RTAXI',
                    'description' => 'Taxi Requested',
                    'created_at' => Carbon::now(),
                ],
                [
                    'display_string' => 'TAXI',
                    'description' => 'Taxi',
                    'created_at' => Carbon::now(),
                ],
                [
                    'display_string' => 'HOLD',
                    'description' => 'Hold Position',
                    'created_at' => Carbon::now(),
                ],
                [
                    'display_string' => 'LINE',
                    'description' => 'Line Up',
                    'created_at' => Carbon::now(),
                ],
                [
                    'display_string' => 'DEPA',
                    'description' => 'Cleared for Takeoff',
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
        DB::table('ground_statuses')->truncate();
    }
}
