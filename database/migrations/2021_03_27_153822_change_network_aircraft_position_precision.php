<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeNetworkAircraftPositionPrecision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            "ALTER TABLE `network_aircraft`
	         CHANGE COLUMN `latitude` `latitude` DOUBLE(8,5) NOT NULL DEFAULT '0.00' COMMENT 'The aircrafts latitude' AFTER `callsign`,
	         CHANGE COLUMN `longitude` `longitude` DOUBLE(8,5) NOT NULL DEFAULT '0.00' COMMENT 'The aircrafts longitude' AFTER `latitude`;"
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
