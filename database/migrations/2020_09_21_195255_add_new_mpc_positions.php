<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewMpcPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add positions
        DB::table('controller_positions')->insert(
            [
                [
                    'callsign' => 'MAN_WI_CTR',
                    'frequency' => '133.050',
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'MAN_S_CTR',
                    'frequency' => '134.420',
                    'created_at' => Carbon::now(),
                ],
            ]
        );

        // Add to top-down orders
        AirfieldService::insertIntoOrderBefore('EGNS', 'MAN_WI_CTR', 'MAN_W_CTR');
        AirfieldService::insertIntoOrderBefore('EGNH', 'MAN_WI_CTR', 'MAN_W_CTR');
        AirfieldService::insertIntoOrderBefore('EGCC', 'MAN_S_CTR', 'MAN_E_CTR');

        // Create new handoff order for some EGCC SIDs
        HandoffService::createNewHandoffOrder(
            'EGCC_SID_SOUTH',
            'EGCC Southbound SIDs',
            [
                'MAN_S_CTR',
                'MAN_E_CTR',
                'MAN_CTR',
                'LON_N_CTR',
                'LON_CTR',
                'EGCC_N_APP'
            ]
        );

        // Assign SIDs to handoff orders
        HandoffService::setHandoffForSid('EGCC', 'LISTO2R', 'EGCC_SID_SOUTH');
        HandoffService::setHandoffForSid('EGCC', 'LISTO2S', 'EGCC_SID_SOUTH');
        HandoffService::setHandoffForSid('EGCC', 'LISTO2Y', 'EGCC_SID_SOUTH');
        HandoffService::setHandoffForSid('EGCC', 'LISTO2Z', 'EGCC_SID_SOUTH');
        HandoffService::setHandoffForSid('EGCC', 'LISTO2Z', 'EGCC_SID_SOUTH');
        HandoffService::setHandoffForSid('EGCC', 'SANBA1R', 'EGCC_SID_SOUTH');
        HandoffService::setHandoffForSid('EGCC', 'SANBA1Y', 'EGCC_SID_SOUTH');
        HandoffService::insertIntoOrderBefore('EGNX_SID_NORTH_27', 'MAN_S_CTR', 'MAN_E_CTR');

        // Update dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the handoff things
        HandoffService::setHandoffForSid('EGCC', 'LISTO2R', 'EGCC_SID_EAST_NORTH');
        HandoffService::setHandoffForSid('EGCC', 'LISTO2S', 'EGCC_SID_EAST_NORTH');
        HandoffService::setHandoffForSid('EGCC', 'LISTO2Y', 'EGCC_SID_EAST_NORTH');
        HandoffService::setHandoffForSid('EGCC', 'LISTO2Z', 'EGCC_SID_EAST_NORTH');
        HandoffService::setHandoffForSid('EGCC', 'LISTO2Z', 'EGCC_SID_EAST_NORTH');
        HandoffService::setHandoffForSid('EGCC', 'SANBA1R', 'EGCC_SID_EAST_NORTH');
        HandoffService::setHandoffForSid('EGCC', 'SANBA1Y', 'EGCC_SID_EAST_NORTH');
        HandoffService::removeFromHandoffOrder('EGNX_SID_NORTH_27', 'MAN_S_CTR');
        DB::table('handoffs')->where('key', 'EGCC_SID_SOUTH')->delete();

        // Remove from top-downs
        AirfieldService::removePositionFromAllTopDowns('MAN_S_CTR');
        AirfieldService::removePositionFromAllTopDowns('MAN_WI_CTR');

        // Remove positions
        DB::table('controller_positions')->whereIn('callsign', ['MAN_WI_CTR', 'MAN_S_CTR'])->delete();

        // Update dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
    }
}
