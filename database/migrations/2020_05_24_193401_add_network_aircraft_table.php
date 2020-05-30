<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('network_aircraft', function (Blueprint $table) {
            $table->string('callsign')->comment('The aircrafts callsign');
            $table->string('latitude')->nullable()->comment('The aircrafts latitude');
            $table->string('longitude')->nullable()->comment('The aircrafts longitude');
            $table->integer('altitude')->nullable()->comment('The aircrafts current altitude');
            $table->unsignedInteger('groundspeed')->nullable()->comment('The aircrafts current ground speed');
            $table->string('planned_aircraft')->nullable()->comment('The aircraft type');
            $table->string('planned_depairport')->nullable()->comment('The departure airport');
            $table->string('planned_destairport')->nullable()->comment('The destination airport');
            $table->string('planned_altitude')->nullable()->comment('The filed cruise altitude');
            $table->string('transponder')->nullable()->comment('The aircrafts current squawk code');
            $table->string('planned_flighttype')->nullable()->comment('The aircrafts flight rules');
            $table->text('planned_route')->nullable()->comment('The aircrafts planned route');
            $table->timestamps();

            $table->primary('callsign');
        });
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
