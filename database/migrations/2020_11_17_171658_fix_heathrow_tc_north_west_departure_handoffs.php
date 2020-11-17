<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;

class FixHeathrowTcNorthWestDepartureHandoffs extends Migration
{
    const HANDOFFS = [
        'EGWU_SID_WEST',
        'EGLL_SID_NORTH_WEST',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::HANDOFFS as $handoff) {
            HandoffService::insertIntoOrderAfter($handoff, 'LON_CL_CTR', 'LTC_M_CTR');
            HandoffService::insertIntoOrderAfter($handoff, 'LON_CE_CTR', 'LON_CL_CTR');
        }
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (self::HANDOFFS as $handoff) {
            HandoffService::removeFromHandoffOrder($handoff, 'LON_CL_CTR');
            HandoffService::removeFromHandoffOrder($handoff, 'LON_CE_CTR');
        }
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
    }
}
