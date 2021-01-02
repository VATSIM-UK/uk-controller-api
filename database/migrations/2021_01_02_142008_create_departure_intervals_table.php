<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartureIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departure_intervals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id')->comment('The type of interval');
            $table->timestamp('expires_at')->comment('The time at which the departure interval expires');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departure_intervals');
    }
}
