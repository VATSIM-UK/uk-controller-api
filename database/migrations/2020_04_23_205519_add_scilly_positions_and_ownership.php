<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddScillyPositionsAndOwnership extends Migration
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
                    [
                        'callsign' => 'EGHE_TWR',
                        'frequency' => 124.87,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'EGHE_APP',
                        'frequency' => 124.87,
                        'created_at' => Carbon::now(),
                    ],
                ]
            );

        AirfieldService::createNewTopDownOrder('EGHE', ['EGHE_TWR', 'EGHE_APP', 'LON_W_CTR', 'LON_CTR']);
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        AirfieldService::deleteTopDownOrder('EGHE');

        DB::table('controller_positions')
            ->whereIn('callsign', ['EGHE_TWR', 'EGHE_APP'])
            ->delete();

        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }
}
