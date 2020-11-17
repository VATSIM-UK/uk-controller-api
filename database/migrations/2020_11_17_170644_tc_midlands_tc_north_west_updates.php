<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TcMidlandsTcNorthWestUpdates extends Migration
{
    // The airfields whose top-downs are affected
    const AIRFIELDS = [
        'EGGW',
        'EGSS',
        'EGSC'
    ];

    // The callsign of TC Midlands
    const POSITION_TO_ADD = 'LTC_M_CTR';

    // The position after which to add TC Midlands
    const POSITION_TO_ADD_AFTER = 'LTC_CTR';

    // The handoff orders affected
    const HANDOFFS = [
        'EGWU_SID_WEST',
        'EGSS_SID_WEST',
        'EGLL_SID_NORTH_WEST',
        'EGGW_SID_WEST_26',
        'EGGW_SID_WEST_08',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Handle airfields
        foreach (self::AIRFIELDS as $airfield) {
            AirfieldService::insertIntoOrderAfter($airfield, self::POSITION_TO_ADD, self::POSITION_TO_ADD_AFTER);
        }

        // Handle handoffs
        foreach (self::HANDOFFS as $handoff) {
            HandoffService::insertIntoOrderAfter($handoff, self::POSITION_TO_ADD, self::POSITION_TO_ADD_AFTER);
        }


        // Handle dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Handle airfields
        foreach (self::AIRFIELDS as $airfield) {
            AirfieldService::removeFromTopDownsOrder($airfield, self::POSITION_TO_ADD);
        }

        // Handle handoffs
        foreach (self::HANDOFFS as $handoff) {
            HandoffService::removeFromHandoffOrder($handoff, self::POSITION_TO_ADD);
        }

        // Handle dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }
}
