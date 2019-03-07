<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmaMslCalculationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tma_msl_calculation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tma_id')->comment('The TMA the calculation is for');
            $table->json('calculation')->comment('How to calculate the MSL');
            $table->timestamps();

            $table->unique('tma_id');
            $table->foreign('tma_id')->references('id')->on('tma');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tma_msl_calculation');
    }
}
