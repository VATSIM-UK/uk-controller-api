<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignedHoldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assigned_holds', function (Blueprint $table) {
            $table->string('callsign')->comment('The aircraft that is holding');
            $table->unsignedBigInteger('navaid_id')->comment('The navaid that the aircraft has been assigned to hold');
            $table->timestamps();

            $table->primary('callsign');
            $table->foreign('callsign')->references('callsign')->on('network_aircraft')->onDelete('cascade');
            $table->foreign('navaid_id')->references('id')->on('navaids')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assigned_holds');
    }
}
