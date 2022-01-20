<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EastMidlandsCargoPriority extends Migration
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
        DB::table('stands')
            ->where('airfield_id', DB::table('airfield')->where('code', 'EGNX')->first()->id)
            ->whereIn('identifier', self::STANDS)
            ->update(['assignment_priority' => 2, 'updated_at' => Carbon::now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('stands')
            ->where('airfield_id', DB::table('airfield')->where('code', 'EGNX')->first()->id)
            ->whereIn('identifier', self::STANDS)
            ->update(['assignment_priority' => 1, 'updated_at' => Carbon::now()]);
    }
}
