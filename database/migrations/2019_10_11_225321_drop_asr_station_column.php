<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAsrStationColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('altimeter_setting_region', function (Blueprint $table) {
            $table->dropColumn('station');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('altimeter_setting_region', function (Blueprint $table) {
            $table->json('station')->after('name');
        });
    }
}
