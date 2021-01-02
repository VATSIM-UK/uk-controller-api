<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartureSidIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departure_sid_intervals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lead_sid_id')->comment('The id of the leading aircraft SID');
            $table->unsignedInteger('following_sid_id')->comment('The id of the following aircraft SID');
            $table->unsignedInteger('interval')->comment('The interval required, in seconds');

            $table->unique(['lead_sid_id', 'following_sid_id'], 'sid_intervals_unique');
            $table->foreign('lead_sid_id')->references('id')->on('sid')->cascadeOnDelete();
            $table->foreign('following_sid_id')->references('id')->on('sid')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departure_sid_intervals');
    }
}
