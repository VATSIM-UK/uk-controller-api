<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirlineTerminalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airline_terminal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('airline_id')->comment('The airline');
            $table->unsignedBigInteger('terminal_id')->comment('The terminal');
            $table->timestamps();

            $table->foreign('airline_id')->references('id')->on('airlines')->cascadeOnDelete();
            $table->foreign('terminal_id')->references('id')->on('terminals')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airline_terminal');
    }
}
