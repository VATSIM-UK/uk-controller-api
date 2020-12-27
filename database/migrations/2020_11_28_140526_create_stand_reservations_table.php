<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stand_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stand_id')->comment('The stand that is reserved');
            $table->string('callsign')->nullable()->comment('The callsign that the stand is reserved for');
            $table->timestamp('start')->index()->comment('The time the reservation starts');
            $table->timestamp('end')->index()->comment('The time the reservation ends');
            $table->timestamps();

            $table->foreign('stand_id')->references('id')->on('stands')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stand_reservations');
    }
}
