<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddElevationToAirfieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('airfield', function (Blueprint $table) {
            $table->unsignedInteger('elevation')
                ->after('longitude')
                ->comment('The airfields elevation above sea level in feet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('airfield', function (Blueprint $table) {
            $table->dropColumn('elevation');
        });
    }
}
