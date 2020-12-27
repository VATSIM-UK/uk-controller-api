<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWingspanToAircraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->double('wingspan', 5, 2)->after('code')->comment('The aircrafts wingspan in ft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->dropColumn('wingspan');
        });
    }
}
