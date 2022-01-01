<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToStandReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stand_reservations', function (Blueprint $table) {
            $table->string('origin', 4)
                ->nullable()
                ->after('callsign')
                ->comment('The origin airfield');
            $table->string('destination', 4)
                ->nullable()
                ->after('origin')
                ->comment('The destination airfield');

            $table->index('origin');
            $table->index('destination');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stand_reservations', function (Blueprint $table) {
            $table->dropColumn('origin');
            $table->dropColumn('destination');
        });
    }
}
