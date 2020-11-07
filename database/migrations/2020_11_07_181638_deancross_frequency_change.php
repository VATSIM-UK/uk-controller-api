<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DeancrossFrequencyChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')
            ->where('callsign', 'SCO_WD_CTR')
            ->update(['frequency' => '133.870']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('controller_positions')
            ->where('callsign', 'SCO_WD_CTR')
            ->update(['frequency' => '133.200']);
    }
}
