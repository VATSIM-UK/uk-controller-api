<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAircraftSpeedGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aircraft_speed_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aircraft_id')->comment('The aircraft');
            $table->unsignedBigInteger('speed_group_id')->comment('The speed group');

            $table->unique(['aircraft_id', 'speed_group_id'], 'aircraft_speed_group_unique');
            $table->foreign('aircraft_id')->references('id')->on('aircraft')->cascadeOnDelete();
            $table->foreign('speed_group_id')->references('id')->on('speed_groups')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aircraft_speed_group');
    }
}
