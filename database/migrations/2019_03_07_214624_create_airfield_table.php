<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAirfieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airfield', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('ICAO code for the airfield');
            $table->unsignedMediumInteger('transition_altitude')->comment('The transition altitude');
            $table->boolean('standard_high')->comment('Is standard pressure (1013) considered high pressure');
            $table->timestamps();

            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airfield');
    }
}
