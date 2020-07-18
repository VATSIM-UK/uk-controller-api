<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightInformationRegionBoundariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_information_region_boundaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flight_information_region_id')
                ->comment('The flight information region the boundary pertains to');
            $table->string('start_latitude')->comment('The latitude of the start point of the segment');
            $table->string('start_longitude')->comment('The longitude of the start point of the segment');
            $table->string('finish_latitude')->comment('The latitude of the finish point of the segment');
            $table->string('finish_longitude')->comment('The longitude of the finish point of the segment');
            $table->string('description');
            $table->timestamps();

            $table->foreign('flight_information_region_id', 'fir_boundaries_fir_id')
                ->references('id')
                ->on('flight_information_regions')
                ->cascadeOnDelete();

            $table->unique(['flight_information_region_id', 'start_latitude', 'start_longitude'], 'boundary_start');
            $table->unique(['flight_information_region_id', 'finish_latitude', 'finish_longitude'], 'boundary_finish');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flight_information_region_boundaries');
    }
}
