<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HeathrowShuttlePriorities extends Migration
{
    const PRIORITIES_TO_CHECK = [
        '501' => 100,
        '502' => 100,
        '503' => 100,
        '505' => 100,
        '506' => 100,
        '507' => 100,
    ];

    const NEW_STANDS = [
        '508' => 100,
        '524' => 101,
        '525' => 101,
        '526' => 101,
        '527' => 101,
        '531' => 102,
        '541' => 103,
        '551' => 104,
        '558' => 105,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $shuttle = DB::table('airlines')->where('icao_code', 'SHT')->first()->id;
        $heathrow = DB::table('airfield')->where('code', 'EGLL')->first()->id;

        // Set priorities for current stands
        foreach (self::PRIORITIES_TO_CHECK as $stand => $priority) {
            DB::table('airline_stand')
                ->where(
                    'stand_id',
                    DB::table('stands')->where('airfield_id', $heathrow)->where('identifier', $stand)->first()->id
                )
                ->where('airline_id', $shuttle)
                ->update(['priority' => $priority]);
        }

        // Add new options
        foreach (self::NEW_STANDS as $stand => $priority) {
            DB::table('airline_stand')
                ->insert(
                    [
                        'stand_id' => DB::table('stands')->where('airfield_id', $heathrow)->where('identifier', $stand)->first()->id,
                        'airline_id' => $shuttle,
                        'priority' => $priority,
                        'created_at' => Carbon::now(),
                    ]
                );
        }

        // Fallback, T5A
        DB::table('airline_terminal')
            ->insert(
                [
                    'airline_id' => $shuttle,
                    'terminal_id' => DB::table('terminals')->where('key', 'EGLL_T5A')->first()->id,
                    'created_at' => Carbon::now(),
                ]
        );
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
