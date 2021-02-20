<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use App\Services\HandoffService;
use App\Services\PrenoteService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDaventryStandalonePosition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the position
        DB::table('controller_positions')->insert(
            [
                'callsign' => 'LON_M_CTR',
                'frequency' => '120.020'
            ]
        );

        // Add it to the top-downs
        AirfieldService::insertIntoOrderBefore('EGSS', 'LON_M_CTR', 'LON_C_CTR');
        AirfieldService::insertIntoOrderBefore('EGGW', 'LON_M_CTR', 'LON_C_CTR');
        AirfieldService::insertIntoOrderBefore('EGSC', 'LON_M_CTR', 'LON_C_CTR');
        AirfieldService::insertIntoOrderBefore('EGBB', 'LON_M_CTR', 'LON_C_CTR');
        AirfieldService::insertIntoOrderBefore('EGNX', 'LON_M_CTR', 'LON_C_CTR');
        AirfieldService::insertIntoOrderBefore('EGTK', 'LON_M_CTR', 'LON_C_CTR');
        AirfieldService::insertIntoOrderBefore('EGTC', 'LON_M_CTR', 'LON_C_CTR');

        // Add it to handoffs
        HandoffService::insertIntoOrderBefore('EGLL_SID_NORTH_WEST', 'LON_M_CTR', 'LON_C_CTR');
        HandoffService::insertIntoOrderBefore('EGSS_SID_WEST', 'LON_M_CTR', 'LON_C_CTR');
        HandoffService::insertIntoOrderBefore('EGGW_SID_WEST_26', 'LON_M_CTR', 'LON_C_CTR');
        HandoffService::insertIntoOrderBefore('EGGW_SID_WEST_08', 'LON_M_CTR', 'LON_C_CTR');
        HandoffService::insertIntoOrderBefore('EGBB_SID', 'LON_M_CTR', 'LON_C_CTR');
        HandoffService::insertIntoOrderBefore('EGNX_SID_NORTH_09', 'LON_M_CTR', 'LON_C_CTR');
        HandoffService::insertIntoOrderBefore('EGNX_SID_SOUTH_09', 'LON_M_CTR', 'LON_C_CTR');
        HandoffService::insertIntoOrderBefore('EGNX_SID_SOUTH_27', 'LON_M_CTR', 'LON_C_CTR');
        HandoffService::insertIntoOrderBefore('EGWU_SID_WEST', 'LON_M_CTR', 'LON_C_CTR');
        HandoffService::insertIntoOrderBefore('EGMC_PDR_EVNAS', 'LON_M_CTR', 'LON_C_CTR');

        // Add it to prenotes
        PrenoteService::insertIntoOrderBefore('EGSS_SID_NUGBO', 'LON_M_CTR', 'LON_C_CTR');
        PrenoteService::insertIntoOrderBefore('PAIRING_ESSEX_LTMA_NORTH_WEST', 'LON_M_CTR', 'LON_C_CTR');

        // Update the dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_PRENOTE');
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
