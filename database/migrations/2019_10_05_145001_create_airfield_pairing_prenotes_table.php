<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAirfieldPairingPrenotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airfield_pairing_prenotes', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->unsignedInteger('origin_airfield_id')->comment('The origin airfield');
            $table->unsignedInteger('destination_airfield_id')->comment('The destination airfield');
            $table->unsignedBigInteger('prenote_id')
                ->comment('The prenote associated with the pairing');
            $table->timestamps();

            // Keys
            $table->unique(['origin_airfield_id', 'destination_airfield_id', 'prenote_id'], 'unique_pairing');
            $table->foreign('origin_airfield_id', 'origin_airfield')
                ->references('id')
                ->on('airfield')
                ->onDelete('cascade');
            $table->foreign('destination_airfield_id', 'destination_airfield')
                ->references('id')
                ->on('airfield')
                ->onDelete('cascade');
            $table->foreign('prenote_id')
                ->references('id')
                ->on('prenotes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airfield_pairing_prenotes');
    }
}
