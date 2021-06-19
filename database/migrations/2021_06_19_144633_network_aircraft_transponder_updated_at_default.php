<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NetworkAircraftTransponderUpdatedAtDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            'ALTER TABLE `network_aircraft` 
                  CHANGE `transponder_last_updated_at` `transponder_last_updated_at` TIMESTAMP'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(
            'ALTER TABLE `network_aircraft` 
                  CHANGE `transponder_last_updated_at` `transponder_last_updated_at` TIMESTAMP NOT NULL'
        );
    }
}
