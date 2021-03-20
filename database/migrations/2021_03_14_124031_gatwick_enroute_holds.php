<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class GatwickEnrouteHolds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TEBRA
        $this->makeHold(
            $this->makeNavaid(
                [
                    'identifier' => 'TEBRA',
                    'latitude' => 'N051.29.20.000',
                    'longitude' => 'E001.36.43.000',
                ]
            ),
            [
                'inbound_heading' => 270,
                'minimum_altitude' => 16000,
                'maximum_altitude' => 22000,
                'turn_direction' => 'left',
                'description' => 'TEBRA',
            ]
        );

        // AMDUT
        $this->makeHold(
            $this->makeNavaid(
                [
                    'identifier' => 'AMDUT',
                    'latitude' => 'N050.40.28.000',
                    'longitude' => 'E000.47.46.000',
                ]
            ),
            [
                'inbound_heading' => 313,
                'minimum_altitude' => 16000,
                'maximum_altitude' => 19000,
                'turn_direction' => 'right',
                'description' => 'AMDUT',
            ]
        );

        // ARNUN - deemed separated from TIMBA
        $arnun = $this->makeHold(
            $this->makeNavaid(
                [
                    'identifier' => 'ARNUN',
                    'latitude' => 'N051.03.26.000',
                    'longitude' => 'E000.55.53.000',
                ]
            ),
            [
                'inbound_heading' => 217,
                'minimum_altitude' => 10000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'ARNUN',
            ]
        );

        $timba = DB::table('navaids')->where('identifier', 'TIMBA')->first()->id;
        $this->makeHoldDeemedSeparated(
            $arnun,
            DB::table('holds')->where('navaid_id', $timba)->first()->id,
            6
        );

        // GWC
        $goodwood = $this->makeHold(
            $this->makeNavaid(
                [
                    'identifier' => 'GWC',
                    'latitude' => 'N050.51.18.780',
                    'longitude' => 'W000.45.24.250',
                ]
            ),
            [
                'inbound_heading' => 176,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 13000,
                'turn_direction' => 'left',
                'description' => 'GWC',
            ]
        );
        $willo = DB::table('navaids')->where('identifier', 'WILLO')->first()->id;
        $this->makeHoldDeemedSeparated(
            $goodwood,
            DB::table('holds')->where('navaid_id', $willo)->first()->id,
            6
        );

        DependencyService::touchDependencyByKey('DEPENDENCY_NAVAIDS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
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

    private function makeHoldDeemedSeparated(int $firstHold, int $secondHold, int $vslInsertDistance)
    {
        DB::table('deemed_separated_holds')->insert(
            [
                [
                    'first_hold_id' => $firstHold,
                    'second_hold_id'=> $secondHold,
                    'vsl_insert_distance' => $vslInsertDistance,
                ],
                [
                    'first_hold_id' => $secondHold,
                    'second_hold_id'=> $firstHold,
                    'vsl_insert_distance' => $vslInsertDistance,
                ]
            ]
        );
    }

    private function makeNavaid(array $navaidData): int
    {
        return DB::table('navaids')->insertGetId(array_merge($navaidData, ['created_at' => Carbon::now()]));
    }

    private function makeHold(int $navaidId, array $publishedHoldData): int
    {
        return DB::table('holds')->insertGetId(
            array_merge($publishedHoldData, ['navaid_id' => $navaidId, 'created_at' => Carbon::now()])
        );
    }
}
