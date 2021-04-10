<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLatLongToAircraftStand extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aircraft_stand', function (Blueprint $table) {
            $table->float('latitude', 8, 5)
                ->after('callsign')
                ->default(0.0)
                ->comment('The latitude at which the aircraft was occupying the stand');
            $table->float('longitude', 8, 5)
                ->after('latitude')
                ->default(0.0)
                ->comment('The longitude at which the aircraft was occupying the stand');

            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aircraft_stand', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn('longitude');
            $table->dropColumn('latitude');
        });
    }
}
