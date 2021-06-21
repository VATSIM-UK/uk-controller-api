<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateNorwichTopdown extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add LON_NE.
        DB::table('controller_positions')
            ->insert(
                [
                    'callsign' => 'LON_NE_CTR',
                    'frequency' => 128.120,
                    'requests_departure_releases' => true,
                    'receives_departure_releases' => true,
                    'created_at' => Carbon::now(),
                ]
            );

        // Handle Norwhich top-down
        AirfieldService::insertIntoOrderBefore(
            'EGSH',
            'LON_NE_CTR',
            'LON_N_CTR'
        );

        // Touch dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS_V2');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
