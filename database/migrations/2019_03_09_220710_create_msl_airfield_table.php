<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMslAirfieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('msl_airfield', function (Blueprint $table) {
            $table->unsignedInteger('airfield_id');
            $table->unsignedSmallInteger('msl');
            $table->timestamp('generated_at');

            $table->primary('airfield_id');
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
        Schema::dropIfExists('msl_airfield');
    }
}
