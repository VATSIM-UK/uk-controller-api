<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavaidNetworkAircraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navaid_network_aircraft', function (Blueprint $table) {
            $table->id();
            $table->string('callsign')->comment('The aircraft');
            $table->unsignedBigInteger('navaid_id')->comment('The navaid the network aircraft is close to');
            $table->dateTime('entered_at')->comment('The time the aircraft entered the hold, in Z');

            $table->unique(['callsign', 'navaid_id'], 'navaid_aircraft_callsign');
            $table->foreign('callsign')->references('callsign')->on('network_aircraft')->cascadeOnDelete();
            $table->foreign('navaid_id')->references('id')->on('navaids')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navaid_network_aircraft');
    }
}
