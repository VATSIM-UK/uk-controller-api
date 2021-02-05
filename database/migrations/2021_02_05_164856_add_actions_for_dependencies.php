<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddActionsForDependencies extends Migration
{
    private const DEPENDENCY_MAP = [
        'DEPENDENCY_AIRFIELD_OWNERSHIP' => 'AirfieldController@getAirfieldOwnershipDependency',
        'DEPENDENCY_CONTROLLER_POSITIONS' => 'ControllerPositionController@getControllerPositionsDependency',
        'DEPENDENCY_HOLDS' => 'HoldController@getAllHolds',
        'DEPENDENCY_INITIAL_ALTITUDES' => 'SidController@getInitialAltitudeDependency',
        'DEPENDENCY_WAKE' => 'AircraftController@getWakeCategoriesDependency',
        'DEPENDENCY_PRENOTE' => 'PrenoteController@getAllPrenotes',
        'DEPENDENCY_ASR' => 'RegionalPressureController@getAltimeterSettingRegions',
        'DEPENDENCY_HANDOFF' => 'HandoffController@getAllHandoffs',
        'DEPENDENCY_SID_HANDOFF' => 'SidController@getSidHandoffsDependency',
        'DEPENDENCY_NAVAIDS' => 'NavaidController',
        'DEPENDENCY_ENROUTE_RELEASE_TYPES' => 'ReleaseController@enrouteReleaseTypeDependency',
        'DEPENDENCY_STANDS' => 'StandController@getStandsDependency',
        'DEPENDENCY_RECAT' => 'AircraftController@getRecatCategoriesDependency',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::DEPENDENCY_MAP as $dependency => $action)
        {
            DB::table('dependencies')
                ->where('key', $dependency)
                ->update(['action' => $action, 'updated_at' => Carbon::now()]);
        }
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
