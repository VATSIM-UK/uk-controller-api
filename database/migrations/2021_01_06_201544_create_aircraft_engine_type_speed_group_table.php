<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAircraftEngineTypeSpeedGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('engine_type_speed_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engine_type_id')->comment('Engine type');
            $table->unsignedBigInteger('speed_group_id')->comment('The speed group');

            $table->unique(['engine_type_id', 'speed_group_id'], 'engine_type_speed_group_unique');
            $table->foreign('engine_type_id')->references('id')->on('engine_types')->cascadeOnDelete();
            $table->foreign('speed_group_id')->references('id')->on('speed_groups')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('engine_type_speed_group');
    }
}
