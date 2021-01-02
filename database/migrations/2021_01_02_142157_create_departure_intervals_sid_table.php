<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartureIntervalsSidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departure_intervals_sid', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('departure_interval_id')->comment('The interval this applies to');
            $table->unsignedInteger('sid_id')->comment('The sids that this interval applies to');
            $table->timestamps();

            $table->foreign('departure_interval_id')->references('id')->on('departure_intervals')->cascadeOnDelete();
            $table->foreign('sid_id')->references('id')->on('sid')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departure_intervals_sid');
    }
}
