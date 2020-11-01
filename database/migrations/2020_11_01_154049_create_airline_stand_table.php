<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirlineStandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airline_stand', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('airline_id')->comment('The airline');
            $table->unsignedBigInteger('stand_id')->comment('The stand');
            $table->string('destination', 4)
                ->nullable()
                ->comment('A full or partial icao of destinations for which this stand should be preferred');
            $table->timestamps();

            $table->foreign('airline_id')->references('id')->on('airlines')->cascadeOnDelete();
            $table->foreign('stand_id')->references('id')->on('stands')->cascadeOnDelete();
            $table->index('destination');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airline_stand');
    }
}
