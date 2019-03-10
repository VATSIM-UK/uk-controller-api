<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tma', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('The TMA name');
            $table->string('description')->comment('Description of the TMA');
            $table->unsignedMediumInteger('transition_altitude');
            $table->boolean('standard_high')->comment('Is standard pressure (1013) considered high pressure');
            $table->timestamps();

            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tma');
    }
}
