<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirProximityMeasuringPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fir_proximity_measuring_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flight_information_region_id');
            $table->double('latitude');
            $table->double('longitude');
            $table->string('description');

            $table->foreign('flight_information_region_id', 'measuring_flight_information_region')
                ->references('id')
                ->on('flight_information_regions')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fir_proximity_measuring_points');
    }
}
