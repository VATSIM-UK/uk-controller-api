<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddLandsEndTopDown extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')->insert(
            [
                'callsign' => 'EGHC_TWR',
                'frequency' => 120.25,
                'created_at' => Carbon::now(),
            ]
        );

        AirfieldService::createNewTopDownOrder(
            'EGHC',
            [
                'EGHC_TWR',
                'LON_W_CTR',
                'LON_CTR',
            ]
        );

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
        AirfieldService::deleteTopDownOrder('EGHC');
        DB::table('controller_positions')
            ->where('callsign', 'EGHC_TWR')
            ->delete();

        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }
}
