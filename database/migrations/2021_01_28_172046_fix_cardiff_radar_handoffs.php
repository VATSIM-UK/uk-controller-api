<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;

class FixCardiffRadarHandoffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create new GD handoff orders
        HandoffService::createNewHandoffOrder(
            'EGGD_SID_BCN_EXMOR_27',
            'Bristol BCN/EXMOR Departures On Runway 27',
            [
                'EGFF_APP',
                'EGGD_APP',
                'LON_WN_CTR',
                'LON_W_CTR',
                'LON_CTR'
            ]
        );

        HandoffService::createNewHandoffOrder(
            'EGGD_SID',
            'All Bristol Sids On 09, BADIM on 27',
            [
                'EGGD_APP',
                'LON_WN_CTR',
                'LON_W_CTR',
                'LON_CTR'
            ]
        );

        // Set GD SIDs to new handoffs
        HandoffService::setHandoffForSid('EGGD', 'BADIM1X', 'EGGD_SID');
        HandoffService::setHandoffForSid('EGGD', 'WOTAN1Z', 'EGGD_SID');
        HandoffService::setHandoffForSid('EGGD', 'BCN1Z', 'EGGD_SID');
        HandoffService::setHandoffForSid('EGGD', 'BCN1X', 'EGGD_SID_BCN_EXMOR_27');
        HandoffService::setHandoffForSid('EGGD', 'EXMOR1Z', 'EGGD_SID');
        HandoffService::setHandoffForSid('EGGD', 'EXMOR1X', 'EGGD_SID_BCN_EXMOR_27');

        // Delete old GD handoff orders
        HandoffService::deleteHandoffByKey('EGGD_SID_27_BADIM');
        HandoffService::deleteHandoffByKey('EGGD_SID_27_BCN');
        HandoffService::deleteHandoffByKey('EGGD_SID_27_EXMOR');
        HandoffService::deleteHandoffByKey('EGGD_SID_09_WOTAN');
        HandoffService::deleteHandoffByKey('EGGD_SID_09_BCN');
        HandoffService::deleteHandoffByKey('EGGD_SID_09_EXMOR');

        // Add FF App to remaining handoffs and delete the wrong one
        HandoffService::insertIntoOrderAfter('EGFF_SID_NORTH', 'EGFF_APP', 'EGFF_L_APP');
        HandoffService::insertIntoOrderAfter('EGFF_SID_SOUTH', 'EGFF_APP', 'EGFF_L_APP');
        HandoffService::removePositionFromAllHandoffs('EGFF_L_APP');

        // Update the dependency
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
        // There is no return
    }
}
