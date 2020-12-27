<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxAircraftIdToStands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->unsignedBigInteger('max_aircraft_id')
                ->after('wake_category_id')
                ->nullable()
                ->comment('The specific maximum aircraft type that can fit on this stand');
            $table->foreign('max_aircraft_id')->references('id')->on('aircraft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->dropForeign('stands_max_aircraft_id_foreign');
            $table->drop('max_aircraft_id');
        });
    }
}
