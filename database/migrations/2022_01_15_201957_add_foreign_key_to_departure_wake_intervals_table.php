<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToDepartureWakeIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departure_wake_intervals', function (Blueprint $table) {
            $table->foreign('measurement_unit_id', 'departure_wake_units')->references('id')->on('measurement_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departure_wake_intervals', function (Blueprint $table) {
            $table->dropForeign('departure_wake_units');
        });
    }
}
