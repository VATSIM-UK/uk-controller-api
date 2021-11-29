<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMissingDatabaseDependencyTables extends Migration
{
    const DEPENDENCIES = [
        'DEPENDENCY_HANDOFFS_V2' => [
            'handoffs',
            'handoff_orders',
        ],
        'DEPENDENCY_PRENOTES_V2' => [
            'prenotes',
            'prenote_orders',
        ],
        'DEPENDENCY_FLIGHT_RULES' => [
            'flight_rules',
        ],
        'DEPENDENCY_AIRFIELD' => [
            'airfield_pairing_prenotes',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::DEPENDENCIES as $dependency => $tables) {
            $tableIds = [];
            foreach ($tables as $table) {
                DB::table('database_tables')->upsert(
                    ['name' => $table, 'created_at' => Carbon::now()],
                    ['name']
                );

                $tableIds[] = DB::table('database_tables')->where('name', $table)->first()->id;
            }

            $dependencyId = DB::table('dependencies')->where('key', $dependency)->first()->id;
            DB::table('database_table_dependency')
                ->upsert(
                    array_map(function (int $tableId) use ($dependencyId) {
                        return [
                            'database_table_id' => $tableId,
                            'dependency_id' => $dependencyId,
                            'created_at' => Carbon::now(),
                        ];
                    }, $tableIds),
                    ['dependency_id', 'table_id']
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
