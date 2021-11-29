<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworkControllerPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_controller_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cid')->comment('The users CID');
            $table->string('callsign')->comment('The controllers callsign');
            $table->decimal('frequency', 6, 3)->comment('The controllers frequency');
            $table->unsignedBigInteger('controller_position_id')->nullable()
                ->comment('The known controller position this position is matched to');
            $table->timestamps();

            $table->foreign('controller_position_id')->references('id')->on('controller_positions')->cascadeOnDelete();
            $table->unique('cid');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('network_controller_positions');
    }
}
