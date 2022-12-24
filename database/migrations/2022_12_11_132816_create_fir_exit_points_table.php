<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fir_exit_points', function (Blueprint $table) {
            $table->id();
            $table->string('exit_point')->unique();
            $table->boolean('internal')->comment('True if the exit point exists between EGTT and EGPX');
            $table->unsignedSmallInteger('exit_direction_start')->comment('The lower bound of the exit direction (clockwise)');
            $table->unsignedSmallInteger('exit_direction_end')->comment('The upper bound of the exit direction (clockwise)');
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
        Schema::dropIfExists('fir_exit_points');
    }
};
