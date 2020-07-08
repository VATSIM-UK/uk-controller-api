<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class Airac1909 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rangeOwnerId = DB::table('squawk_unit')->where('unit', 'EGGD')
            ->select(['squawk_range_owner_id'])
            ->first()
            ->squawk_range_owner_id;

        DB::table('squawk_range')->where('squawk_range_owner_id', $rangeOwnerId)
            ->where('start', '5071')
            ->update(['start' => '5072']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $rangeOwnerId = DB::table('squawk_unit')->where('unit', 'EGGD')
            ->select(['squawk_range_owner_id'])
            ->first()
            ->squawk_range_owner_id;

        DB::table('squawk_range')->where('squawk_range_owner_id', $rangeOwnerId)
            ->where('start', '5072')
            ->update(['start' => '5071']);
    }
}
