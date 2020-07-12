<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworkAircraftFirEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_aircraft_fir_events', function (Blueprint $table) {
            $table->id();
            $table->string('callsign')->comment('The aircrafts callsign');
            $table->unsignedBigInteger('flight_information_region_id')->comment('The FIR in question');
            $table->string('event_type')->comment('Entry, exit, etc');
            $table->json('metadata')->comment('Any other data, position entered etc');
            $table->timestamps();


            $table->foreign('callsign')
                ->references('callsign')
                ->on('network_aircraft')
                ->cascadeOnDelete();

            $table->foreign('flight_information_region_id')
                ->references('id')
                ->on('flight_information_regions')
                ->cascadeOnDelete();

            $table->index('event_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('network_aircraft_fir_events');
    }
}
