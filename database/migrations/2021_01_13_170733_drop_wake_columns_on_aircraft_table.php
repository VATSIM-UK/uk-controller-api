<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropWakeColumnsOnAircraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->dropForeign('aircraft_wake_category_id_foreign');
            $table->dropForeign('aircraft_recat_category_id_foreign');
            $table->dropColumn('wake_category_id');
            $table->dropColumn('recat_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
