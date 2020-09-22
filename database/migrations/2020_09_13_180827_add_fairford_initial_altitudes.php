<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFairfordInitialAltitudes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the airfield
        $fairford = DB::table('airfield')
            ->insertGetId(
                [
                    'code' => 'EGVA',
                    'transition_altitude' => 3000,
                    'standard_high' => true,
                    'msl_calculation' => json_encode(
                        [
                            'type' => 'direct',
                            'airfield' => 'EGVA',
                        ]
                    ),
                    'created_at' => Carbon::now(),
                ]
            );

        // Add the controllers
        DB::table('controller_positions')
            ->insert(
                [
                    [
                        'callsign' => 'EGVA_GND',
                        'frequency' => '121.170',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'EGVA_TWR',
                        'frequency' => '124.800',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'EGVA_D_APP',
                        'frequency' => '134.550',
                        'created_at' => Carbon::now(),
                    ],
                ]
            );

        // Add top down
        AirfieldService::createNewTopDownOrder(
            'EGVA',
            [
                'EGVA_GND',
                'EGVA_TWR',
                'EGVA_D_APP',
                'EGVV_CTR',
            ]
        );

        // Add the SIDs
        DB::table('sid')
            ->insert(
                [
                    [
                        'airfield_id' => $fairford,
                        'identifier' => 'GIBMI1A',
                        'initial_altitude' => 5000,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'airfield_id' => $fairford,
                        'identifier' => 'MAXOB1A',
                        'initial_altitude' => 5000,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'airfield_id' => $fairford,
                        'identifier' => 'MAXOB1B',
                        'initial_altitude' => 5000,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'airfield_id' => $fairford,
                        'identifier' => 'OKDID1A',
                        'initial_altitude' => 5000,
                        'created_at' => Carbon::now(),
                    ],
                ]
            );

        // Add the handoff order and assign to SIDs
        HandoffService::createNewHandoffOrder(
            'EGVA_SID',
            'Fairford Departures',
            [
                'EGVV_CTR',
            ]
        );
        HandoffService::setHandoffForSid('EGVA', 'GIBMI1A', 'EGVA_SID');
        HandoffService::setHandoffForSid('EGVA', 'MAXOB1A', 'EGVA_SID');
        HandoffService::setHandoffForSid('EGVA', 'MAXOB1B', 'EGVA_SID');
        HandoffService::setHandoffForSid('EGVA', 'OKDID1A', 'EGVA_SID');

        // Update dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('airfield')->where('code', 'EGVA')->delete();
        DB::table('controller_positions')->whereIn(
            'callsign',
            [
                'EGVA_GND',
                'EGVA_TWR',
                'EGVA_D_APP',
            ]
        );

        // Update dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
    }
}
