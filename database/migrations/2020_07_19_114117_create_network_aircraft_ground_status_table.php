<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworkAircraftGroundStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'ground_status_network_aircraft',
            function (Blueprint $table) {
                $table->id();
                $table->string('callsign')->comment('The aircraft');
                $table->unsignedBigInteger('ground_status_id')->comment('The ground status');
                $table->timestamps();

                $table->unique('callsign');
                $table->foreign('callsign')->references('callsign')->on(
                    'network_aircraft'
                )->cascadeOnDelete();
                $table->foreign('ground_status_id')->references('id')->on(
                    'ground_statuses'
                )->cascadeOnDelete();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ground_status_network_aircraft');
    }
}
