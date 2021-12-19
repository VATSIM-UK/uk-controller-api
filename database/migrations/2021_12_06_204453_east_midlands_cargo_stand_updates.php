<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EastMidlandsCargoStandUpdates extends Migration
{
    const STANDS = [
        '70',
        '70L',
        '70R',
        '71',
        '72',
        '73',
        '73L',
        '74',
        '74L',
        '75',
        '75R',
        '76',
        '76L',
        '76R',
        '77',
        '77L',
        '77R',
        '78',
        '78L',
        '78R',
        '78X',
        '79',
        '80',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $cargoType = DB::table('stand_types')->where('key', 'CARGO')->first()->id;
        $eastMids = DB::table('airfield')->where('code', 'EGNX')->first()->id;

        DB::table('stands')
            ->where('airfield_id', $eastMids)
            ->whereIn('identifier', self::STANDS)
            ->update(['updated_at' => Carbon::now(), 'type_id' => $cargoType]);
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
