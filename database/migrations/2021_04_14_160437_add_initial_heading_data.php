<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddInitialHeadingData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Heathrow CPT 09
        DB::table('sid')
            ->where('identifier', 'CHK')
            ->update(['initial_heading' => 220]);

        // KK BIG 26
        DB::table('sid')
            ->whereIn('identifier', ['BIG26L', 'BIG26R'])
            ->update(['initial_heading' => 75]);

        // KK BIG 08
        DB::table('sid')
            ->whereIn('identifier', ['BIG08L', 'BIG08R'])
            ->update(['initial_heading' => 90]);
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
