<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metars', function (Blueprint $table) {
            $table->id();
            $table->text('raw')->comment('The raw METAR text');
            $table->unsignedInteger('airfield_id')->unique()->comment('The airfield the METAR applies to');
            $table->unsignedInteger('qnh')
                ->nullable()
                ->comment('The parsed QNH, converted from altimeter if necessary');
            $table->timestamps();

            $table->foreign('airfield_id')->references('id')->on('airfield')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('metars');
    }
}