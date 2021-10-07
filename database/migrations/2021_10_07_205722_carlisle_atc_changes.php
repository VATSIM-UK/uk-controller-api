<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CarlisleAtcChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AirfieldService::removeFromTopDownsOrder('EGNC', 'EGNC_APP');
        DB::table('controller_positions')->where('callsign', 'EGNC_APP')->delete();

        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('CONTROLLER_POSITIONS_V2');
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
