<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCallsignToSquawkAllocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('squawk_allocation', function ($table) {
            $table->string('callsign', 10)->after('id')->comment('The callsign that the squawk is allocated to.');
            $table->unique('callsign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('squawk_allocation', function ($table) {
            $table->dropColumn('callsign');
        });
    }
}
