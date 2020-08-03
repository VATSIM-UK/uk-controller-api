<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNetworkAircraftCoordinateType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('network_aircraft', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });

        Schema::table('network_aircraft', function (Blueprint $table) {
            $table->float('latitude')->after('callsign')->default(0.0)->comment('The aircrafts latitude');
            $table->float('longitude')->after('latitude')->default(0.0)->comment('The aircrafts longitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('network_aircraft', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });

        Schema::table('network_aircraft', function (Blueprint $table) {
            $table->string('latitude')->after('callsign')->default('0.0')->comment('The aircrafts latitude');
            $table->string('longitude')->after('latitude')->default('0.0')->comment('The aircrafts longitude');
        });
    }
}
