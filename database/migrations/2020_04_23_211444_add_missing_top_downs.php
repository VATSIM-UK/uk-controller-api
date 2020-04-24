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
                        'callsign' => 'EGFE_R_TWR',
                        'frequency' => 122.2,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'EGFH_R_TWR',
                        'frequency' => 119.7,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'EGHR_I_TWR',
                        'frequency' => 124.45,
                        'created_at' => Carbon::now(),
                    ],
                ]
            );

        // Add all the top downs
        AirfieldService::createNewTopDownOrder('EGEO', ['EGEO_I_TWR', 'SCO_W_CTR', 'SCO_WD_CTR', 'SCO_CTR']);
        AirfieldService::createNewTopDownOrder('EGPU', ['EGPU_I_TWR', 'SCO_W_CTR', 'SCO_WD_CTR', 'SCO_CTR']);
        AirfieldService::createNewTopDownOrder('EGPR', ['EGPR_I_TWR', 'SCO_W_CTR', 'SCO_WD_CTR', 'SCO_CTR']);
        AirfieldService::createNewTopDownOrder('EGPL',
            ['EGPL_I_TWR', 'EGPL_TWR', 'EGPL_APP', 'SCO_W_CTR', 'SCO_WD_CTR', 'SCO_CTR']);
        AirfieldService::createNewTopDownOrder(
            'EGTO', [
            'EGTO_I_TWR',
            'THAMES_APP',
            'LTC_SE_CTR',
            'LTC_S_CTR',
            'LTC_CTR',
            'LON_D_CTR',
            'LON_S_CTR',
            'LON_SC_CTR',
            'LON_CTR',
        ]);
        AirfieldService::createNewTopDownOrder('EGOV', ['EGOV_TWR', 'EGOV_P_APP', 'EGOV_R_APP', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGXC', ['EGXC_GND', 'EGXC_TWR', 'EGXC_P_APP', 'EGXC_R_APP', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGYD', ['EGYD_GND', 'EGYD_TWR', 'EGYD_P_APP', 'EGYD_R_APP', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGFE', ['EGFE_R_TWR']);
        AirfieldService::createNewTopDownOrder('EGFH', ['EGFH_R_TWR']);
        AirfieldService::createNewTopDownOrder('EGHR', ['EGHR_I_TWR']);
        AirfieldService::createNewTopDownOrder('EGYM', ['EGYM_TWR', 'EGYM_P_APP', 'EGYM_R_APP', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGYD', ['EGYD_GND', 'EGYD_TWR', 'EGYD_P_APP', 'EGYD_R_APP', 'EGVV_CTR']);

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
                    'EGFE_R_TWR',
                    'EGFH_R_TWR',
                    'EGHR_I_TWR',
                ]
            )
            ->delete();

        // Delete all the top-downs
        AirfieldService::deleteTopDownOrder('EGEO');
        AirfieldService::deleteTopDownOrder('EGPU');
        AirfieldService::deleteTopDownOrder('EGPR');
        AirfieldService::deleteTopDownOrder('EGPL');
        AirfieldService::deleteTopDownOrder('EGTO');
        AirfieldService::deleteTopDownOrder('EGOV');
        AirfieldService::deleteTopDownOrder('EGFE');
        AirfieldService::deleteTopDownOrder('EGFH');
        AirfieldService::deleteTopDownOrder('EGHR');
        AirfieldService::deleteTopDownOrder('EGYM');

        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
    }
}
