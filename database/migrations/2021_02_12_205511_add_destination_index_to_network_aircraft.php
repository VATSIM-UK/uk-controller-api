<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDestinationIndexToNetworkAircraft extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('network_aircraft', function (Blueprint $table) {
            $table->index('planned_destairport');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('network_aircraft', function (Blueprint $table) {
            $table->dropIndex('network_aircraft_planned_destairport_index');
        });
    }
}
