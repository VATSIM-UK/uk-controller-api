<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateControllerPositionsV2Dependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dependencies')->insert(
            [
                'key' => 'DEPENDENCY_CONTROLLER_POSITIONS_V2',
                'action' => 'ControllerPositionController@getControllerPositionsDependency',
                'local_file' => 'controller-positions-v2.json',
                'created_at' => Carbon::now(),
            ]
        );

        DB::table('dependencies')->where('key', 'DEPENDENCY_CONTROLLER_POSITIONS')
            ->update(['action' => 'ControllerPositionController@getLegacyControllerPositionsDependency']);

        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS_V2');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dependencies')->where('key', 'DEPENDENCY_CONTROLLER_POSITIONS_V2')
            ->delete();
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
    }
}
