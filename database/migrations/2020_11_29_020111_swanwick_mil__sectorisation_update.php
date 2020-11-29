<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SwanwickMilSectorisationUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')
            ->where('callsign', 'EGVV_CTR')
            ->update(['frequency' => '133.90']);

        DB::table('controller_positions')
            ->insert([
                'callsign' => 'EGVV_E_CTR',
                'frequency' => '133.32',
                'created_at' => Carbon::now(),
            ]);

        DB::table('controller_positions')
            ->insert([
                'callsign' => 'EGVV_W_CTR',
                'frequency' => '127.45',
                'created_at' => Carbon::now(),
            ]);

        DB::table('controller_positions')
            ->insert([
                'callsign' => 'EGVV_N_CTR',
                'frequency' => '136.37',
                'created_at' => Carbon::now(),
            ]);

        DB::table('controller_positions')
            ->where('callsign', 'EGQQ_CTR')
            ->delete();

        AirfieldService::createNewTopDownOrder('EGOV', ['EGOV_TWR', 'EGOV_P_APP', 'EGOV_R_APP', 'EGVV_W_CTR', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGXC', ['EGXC_GND', 'EGXC_TWR', 'EGXC_P_APP', 'EGXC_APP', 'EGVV_E_CTR', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGYD', ['EGYD_GND', 'EGYD_TWR', 'EGYD_P_APP', 'EGYD_R_APP', 'EGVV_E_CTR', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGYM', ['EGYM_TWR', 'EGYM_P_APP', 'EGYM_R_APP', 'EGVV_E_CTR', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGVN', ['EGVN_GND', 'EGVN_TWR', 'EGVN_F_APP', 'EGVN_APP', 'EGVV_W_CTR', 'EGVV_CTR']);

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
        DB::table('controller_positions')
            ->where('callsign', 'EGVV_CTR')
            ->update(['frequency' => '135.15']);
        
        DB::table('controller_positions')
            ->where('callsign', 'EGVV_E_CTR')
            ->delete();
        
        DB::table('controller_positions')
            ->where('callsign', 'EGVV_W_CTR')
            ->delete();

        DB::table('controller_positions')
            ->where('callsign', 'EGVV_N_CTR')
            ->delete();

        DB::table('controller_positions')
            ->insert([
                'callsign' => 'EGQQ_CTR',
                'frequency' => 134.3,
                'created_at' => Carbon::now(),
            ])

        AirfieldService::createNewTopDownOrder('EGOV', ['EGOV_TWR', 'EGOV_P_APP', 'EGOV_R_APP', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGXC', ['EGXC_GND', 'EGXC_TWR', 'EGXC_P_APP', 'EGXC_APP', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGYD', ['EGYD_GND', 'EGYD_TWR', 'EGYD_P_APP', 'EGYD_R_APP', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGYM', ['EGYM_TWR', 'EGYM_P_APP', 'EGYM_R_APP', 'EGVV_CTR']);
        AirfieldService::createNewTopDownOrder('EGVN', ['EGVN_GND', 'EGVN_TWR', 'EGVN_F_APP', 'EGVN_APP', 'EGVV_CTR']);

        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }
}
