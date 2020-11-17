<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPcTrent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')
            ->insert(
                [
                    'callsign' => 'MAN_T_CTR',
                    'frequency' => '119.520',
                    'created_at' => Carbon::now(),
                ]
            );
        HandoffService::insertIntoOrderBefore('EGNX_SID_NORTH_27', 'MAN_T_CTR', 'MAN_S_CTR');
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        HandoffService::removePositionFromAllHandoffs('MAN_T_CTR');
        DB::table('controller_positions')->where('callsign', 'MAN_T_CTR')->delete();
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
    }
}
