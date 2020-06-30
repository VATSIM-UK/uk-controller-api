<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNetworkAircraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `network_aircraft` (
                `callsign` VARCHAR(255) NOT NULL COMMENT 'The aircrafts callsign',
                `latitude` VARCHAR(255) NULL COMMENT 'The aircrafts latitude',
                `longitude` VARCHAR(255) NULL COMMENT 'The aircrafts longitude',
                `altitude` int NULL COMMENT 'The aircrafts current altitude',
                `groundspeed` INT UNSIGNED NULL COMMENT 'The aircrafts current ground speed',
                `planned_aircraft` VARCHAR(255) NULL COMMENT 'The aircraft type',
                `planned_depairport` VARCHAR(255) NULL COMMENT 'The departure airport',
                `planned_destairport` VARCHAR(255) NULL COMMENT 'The destination airport',
                `planned_altitude` VARCHAR(255) NULL COMMENT 'The filed cruise altitude',
                `transponder` VARCHAR(255) NULL COMMENT 'The aircrafts current squawk code',
                `planned_flighttype` VARCHAR(255) NULL COMMENT 'The aircrafts flight rules',
                `planned_route` text NULL COMMENT 'The aircrafts planned route',
                `created_at` TIMESTAMP NULL, `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`callsign`) USING BTREE
            )
            DEFAULT CHARACTER SET utf8mb4 COLLATE 'utf8mb4_unicode_ci';"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('network_aircraft');
    }
}
