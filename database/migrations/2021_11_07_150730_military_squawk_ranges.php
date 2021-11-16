<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MilitarySquawkRanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('unit_discrete_squawk_ranges')
            ->where('unit', 'EGXE')
            ->update(['last' => '0437', 'updated_at' => Carbon::now()]);

        DB::table('unit_discrete_squawk_ranges')
            ->insert(['unit' => 'EGDR', 'first' => '7356','last' => '7356', 'created_at' => Carbon::now()]);

        DB::table('unit_discrete_squawk_ranges')
            ->insert(['unit' => 'EGDR', 'first' => '7360','last' => '7367', 'created_at' => Carbon::now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('unit_discrete_squawk_ranges')
            ->where('unit', 'EGXE')
            ->update(['last' => '0426', 'updated_at' => Carbon::now()]);

        DB::table('unit_discrete_squawk_ranges')
            ->where('unit', 'EGDR')
            ->where('first', '7356')
            ->delete();

        DB::table('unit_discrete_squawk_ranges')
            ->where('unit', 'EGDR')
            ->where('first', '7360')
            ->delete();
    }
}
