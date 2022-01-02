<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAirfieldIdFromSidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sid', function (Blueprint $table) {
            $table->dropForeign('sid_airfield_id_foreign');
            $table->dropColumn('airfield_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sid', function (Blueprint $table) {
        });
    }
}
