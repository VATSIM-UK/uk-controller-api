<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionalPressureSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regional_pressure_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('altimeter_setting_region_id')->comment('The ASR for which the RPS relates');
            $table->unsignedSmallInteger('value')->comment('The RPS value');
            $table->timestamps();

            // The ASR
            $table->foreign('altimeter_setting_region_id', 'rps_asr_id')
                ->references('id')
                ->on('altimeter_setting_region')
                ->onDelete('cascade');

            $table->unique('altimeter_setting_region_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regional_pressure_settings');
    }
}
