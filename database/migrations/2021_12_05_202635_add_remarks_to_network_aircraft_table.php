<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemarksToNetworkAircraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('network_aircraft', function (Blueprint $table) {
            $table->text('remarks')
                ->nullable()
                ->after('planned_route')
                ->comment('Any remarks on the flightplan');
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
            $table->dropColumn('remarks');
        });
    }
}
