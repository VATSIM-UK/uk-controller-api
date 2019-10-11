<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAltimeterSettingRegionAirfieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('altimeter_setting_region_airfield', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('altimeter_setting_region_id');
            $table->unsignedInteger('airfield_id');

            // Keys
            $table->unique(['altimeter_setting_region_id', 'airfield_id'], 'asr_airfield');
            $table->foreign('altimeter_setting_region_id', 'asr_id')
                ->references('id')
                ->on('altimeter_setting_region')
                ->onDelete('cascade');
            $table->foreign('airfield_id', 'airfield_id')
                ->references('id')
                ->on('airfield')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('altimeter_setting_region_airfield');
    }
}
