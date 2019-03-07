<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAirfieldMslCalculationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airfield_msl_calculation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('airfield_id')->comment('The airfield the calculation is for');
            $table->json('calculation')->comment('How to calculate the MSL');
            $table->timestamps();

            $table->unique('airfield_id');
            $table->foreign('airfield_id')->references('id')->on('airfield');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airfield_msl_calculation');
    }
}
