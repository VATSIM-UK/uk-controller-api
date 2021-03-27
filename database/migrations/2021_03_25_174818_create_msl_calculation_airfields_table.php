<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMslCalculationAirfieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('msl_calculation_airfields', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('airfield_id')
                ->comment('The airfield that the MSL is for');
            $table->unsignedInteger('msl_airfield_id')
                ->comment('The airfield that should be considered when calculating MSL');

            $table->unique(['airfield_id', 'msl_airfield_id'], 'msl_calculation_airfields_unique');
            $table->foreign('airfield_id', 'msl_calculation_airfields_airfield')
                ->references('id')
                ->on('airfield')
                ->cascadeOnDelete();
            $table->foreign('msl_airfield_id', 'msl_calculation_airfields_additional')
                ->references('id')
                ->on('airfield')
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
        Schema::dropIfExists('msl_calculation_airfields');
    }
}
