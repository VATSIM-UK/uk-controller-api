<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControllerPositionAdditionalCallsignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controller_position_alternative_callsigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('controller_position_id');
            $table->string('callsign')->comment('The callsign');
            $table->timestamps();

            $table->foreign('controller_position_id', 'controller_position_alternative_id')
                ->references('id')->on('controller_positions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('controller_position_alternative_callsigns');
    }
}
