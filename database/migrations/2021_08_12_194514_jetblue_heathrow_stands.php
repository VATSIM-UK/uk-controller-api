<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class JetblueHeathrowStands extends Migration
{
    const STANDS = [
        '218',
        '218L',
        '218R',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $heathrow = DB::table('airfield')->where('code', 'EGLL')->first()->id;
        $jetBlue = DB::table('airlines')->where('icao_code', 'JBU')->first()->id;

        // Give them a specific stand
        foreach (self::STANDS as $stand) {
            DB::table('airline_stand')->insert(
                [
                    'airline_id' => $jetBlue,
                    'stand_id' => DB::table('stands')->where('airfield_id', $heathrow)->where(
                        'identifier',
                        $stand
                    )->first()->id,
                    'created_at' => Carbon::now(),
                ]
            );
        }

        // Give them T2A as fallback
        DB::table('airline_terminal')
            ->insert(
                [
                    'airline_id' => $jetBlue,
                    'terminal_id' => DB::table('terminals')->where('key', 'EGLL_T2A')->first()->id,
                    'created_at' => Carbon::now(),
                ]
            );

        // Update the dependency
        DependencyService::touchDependencyByKey('DEPENDENCY_STANDS');
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
