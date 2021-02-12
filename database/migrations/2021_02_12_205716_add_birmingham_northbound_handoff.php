<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;

class AddBirminghamNorthboundHandoff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create handoff
        HandoffService::createNewHandoffOrder(
            'EGBB_SID_NORTHBOUND',
            'Birmingham Northbound SIDs',
            [
                'EGBB_APP',
                'LTC_MC_CTR',
                'LTC_M_CTR',
                'LON_CW_CTR',
                'LON_M_CTR',
                'LON_C_CTR',
                'LON_SC_CTR',
                'LON_CTR',
                'MAN_T_CTR',
                'MAN_S_CTR',
                'MAN_E_CTR',
                'MAN_CTR',
            ]
        );

        // Assign to SIDs
        HandoffService::setHandoffForSid('EGBB', 'LUVUM1L', 'EGBB_SID_NORTHBOUND');
        HandoffService::setHandoffForSid('EGBB', 'LUVUM1M', 'EGBB_SID_NORTHBOUND');
        HandoffService::setHandoffForSid('EGBB', 'TNT1L', 'EGBB_SID_NORTHBOUND');
        HandoffService::setHandoffForSid('EGBB', 'TNT1K', 'EGBB_SID_NORTHBOUND');
        HandoffService::setHandoffForSid('EGBB', 'TNT4G', 'EGBB_SID_NORTHBOUND');
        HandoffService::setHandoffForSid('EGBB', 'TNT4D', 'EGBB_SID_NORTHBOUND');
        HandoffService::setHandoffForSid('EGBB', 'TNT6E', 'EGBB_SID_NORTHBOUND');

        // Bump dependencies
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
        //
    }
}
