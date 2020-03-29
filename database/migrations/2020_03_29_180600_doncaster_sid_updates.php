<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DoncasterSidUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('sid')
            ->where('identifier', 'ROGAG1A')
            ->update(['identifier' => 'ROGAG2A']);

        DB::table('sid')
            ->where('identifier', 'ROGAG1C')
            ->update(['identifier' => 'ROGAG2C']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sid')
            ->where('identifier', 'ROGAG2A')
            ->update(['identifier' => 'ROGAG1A']);

        DB::table('sid')
            ->where('identifier', 'ROGAG2C')
            ->update(['identifier' => 'ROGAG1C']);
    }
}
