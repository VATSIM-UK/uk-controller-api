<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class LutonSidUpdates extends Migration
{
    const SIDS = [
        'CPT3B' => 'CPT4B',
        'CPT6C' => 'CPT7C',
        'DET2Y' => 'DET3Y',
        'DET7B' => 'DET8B',
        'DET6C' => 'DET7C',
        'MATCH2Y' => 'MATCH3Y',
        'MATCH2B' => 'MATCH3B',
        'MATCH1C' => 'MATCH2C',
        'OLNEY1B' => 'OLNEY2B',
        'OLNEY1C' => 'OLNEY2C',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $luton = DB::table('airfield')->where('code', 'EGGW')->pluck('id')->first();
        foreach (self::SIDS as $old => $new) {
            DB::table('sid')->where('airfield_id', $luton)->where('identifier', $old)->update(['identifier' => $new]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $luton = DB::table('airfield')->where('code', 'EGGW')->pluck('id')->first();
        foreach (self::SIDS as $old => $new) {
            DB::table('sid')->where('airfield_id', $luton)->where('identifier', $new)->update(['identifier' => $old]);
        }
    }
}
