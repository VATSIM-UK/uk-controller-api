<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPcPenil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the position
        DB::table('controller_positions')
            ->insert(
                [
                    'callsign' => 'MAN_WP_CTR',
                    'frequency' => '126.870',
                    'created_at' => Carbon::now(),
                ]
            );

        // Add to top-downs and handoff orders
        AirfieldService::updateAllTopDownsWithPosition('MAN_WL_CTR', 'MAN_WP_CTR', false);
        AirfieldService::updateAllTopDownsWithPosition('MAN_WI_CTR', 'MAN_WP_CTR', false);
        HandoffService::updateAllHandoffsWithPosition('MAN_WL_CTR', 'MAN_WP_CTR', false);
        HandoffService::updateAllHandoffsWithPosition('MAN_WI_CTR', 'MAN_WP_CTR', false);

        // Poke dependencies
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
        // Remove from handoffs, top-downs and delete position
        HandoffService::removePositionFromAllHandoffs('MAN_T_CTR');
        AirfieldService::removePositionFromAllTopDowns('MAN_WP_CTR');
        DB::table('controller_positions')->where('callsign', 'MAN_WP_CTR')->delete();

        // Poke dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }
}
