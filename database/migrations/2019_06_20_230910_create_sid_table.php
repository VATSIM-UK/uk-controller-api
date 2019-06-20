<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sid', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identifier');
            $table->unsignedInteger('airfield_id');
            $table->unsignedSmallInteger('initial_altitude');
            $table->timestamps();

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
        Schema::dropIfExists('sid');
    }
}
