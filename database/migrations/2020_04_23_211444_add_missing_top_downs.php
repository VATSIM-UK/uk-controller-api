<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMissingTopDowns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add all the positions
        DB::table('controller_positions')
            ->insert(
                [
                    [
                        'callsign' => 'EGEO_I_TWR',
                        'frequency' => 118.05,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'EGPU_I_TWR',
                        'frequency' => 122.7,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'EGPR_I_TWR',
                        'frequency' => 118.08,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'EGPL_I_TWR',
                        'frequency' => 125.900,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'EGPL_TWR',
                        'frequency' => 119.2,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'EGPL_APP',
                        'frequency' => 119.2,
                        'created_at' => Carbon::now(),
                    ],
                ]
            );

        // Add all the top downs
        AirfieldService::createNewTopDownOrder('EGEO', ['EGEO_I_TWR', 'SCO_W_CTR', 'SCO_WD_CTR', 'SCO_CTR']);
        AirfieldService::createNewTopDownOrder('EGPU', ['EGPU_I_TWR', 'SCO_W_CTR', 'SCO_WD_CTR', 'SCO_CTR']);
        AirfieldService::createNewTopDownOrder('EGPR', ['EGPR_I_TWR', 'SCO_W_CTR', 'SCO_WD_CTR', 'SCO_CTR']);
        AirfieldService::createNewTopDownOrder('EGPL', ['EGPL_I_TWR', 'EGPL_TWR', 'EGPL_APP', 'SCO_W_CTR', 'SCO_WD_CTR', 'SCO_CTR']);

        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete all the positions
        DB::table('controller_positions')
            ->whereIn(
                'callsign',
                [
                    'EGEO_I_TWR',
                    'EGPU_I_TWR',
                    'EGPR_I_TWR',
                    'EGPL_I_TWR',
                    'EGPL_TWR',
                    'EGPL_APP',
                ]
            )
            ->delete();

        // Delete all the top-downs
        AirfieldService::deleteTopDownOrder('EGEO');
        AirfieldService::deleteTopDownOrder('EGPU');
        AirfieldService::deleteTopDownOrder('EGPR');
        AirfieldService::deleteTopDownOrder('EGPL');

        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
    }
}
