<?php

use App\Models\Database\DatabaseTable;
use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDependencyConcernedTables extends Migration
{
    const DEPENDENCY_MAP = [
        'DEPENDENCY_HOLDS' => [
            'holds',
            'deemed_separated_holds',
        ],
        'DEPENDENCY_PRENOTE' => [
            'prenotes',
            'airfield_pairing_prenotes',
        ],
        'DEPENDENCY_WAKE' => [
            'wake_categories',
        ],
        'DEPENDENCY_ASR' => [
            'altimeter_setting_region',
        ],
        'DEPENDENCY_NAVAIDS' => [
            'navaids',
        ],
        'DEPENDENCY_ENROUTE_RELEASE_TYPES' => [
            'enroute_release_types',
        ],
        'DEPENDENCY_STANDS' => [
            'stands',
        ],
        'DEPENDENCY_RECAT' => [
            'wake_categories',
        ],
        'DEPENDENCY_AIRFIELD' => [
            'top_downs',
            'speed_groups',
            'speed_group_speed_group',
        ],
        'DEPENDENCY_DEPARTURE_SID_GROUPS' => [
            'sid_departure_interval_groups',
            'sid_departure_interval_group_sid_departure_interval_group',
        ],
        'DEPENDENCY_WAKE_SCHEME' => [
            'wake_category_schemes',
        ],
        'DEPENDENCY_SIDS' => [
            'sid',
            'sid_prenotes',
        ],
        'DEPENDENCY_CONTROLLER_POSITIONS_V2' => [
            'controller_positions',
            'top_downs',
        ],
        'DEPENDENCY_DEPARTURE_WAKE_UK' => [
            'controller_positions',
            'top_downs',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::DEPENDENCY_MAP as $dependency => $tables) {
            DB::table('database_tables')->upsert(
                array_map(function (string $table) {return ['name' => $table, 'created_at' => Carbon::now()];}, $tables),
                ['name']
            );
            DependencyService::setConcernedTablesForDependency(
                $dependency,
                $tables
            );
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
