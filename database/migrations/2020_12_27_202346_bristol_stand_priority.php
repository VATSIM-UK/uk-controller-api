<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class BristolStandPriority extends Migration
{
    private const STANDS_TO_UPDATE = [
        '7N',
        '26S',
        '29',
        '30',
        '32',
        '33',
        '34',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $bristol = DB::table('airfield')->where('code', 'EGGD')->first()->id;
        DB::table('stands')->where('airfield_id', $bristol)
            ->whereIn('identifier', self::STANDS_TO_UPDATE)
            ->update(['general_use' => false, 'updated_at' => Carbon::now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $bristol = DB::table('airfield')->where('code', 'EGGD')->first()->id;
        DB::table('stands')->where('airfield_id', $bristol)
            ->whereIn('identifier', self::STANDS_TO_UPDATE)
            ->update(['general_use' => true, 'updated_at' => Carbon::now()]);
    }
}
