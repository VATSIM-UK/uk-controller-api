<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDepartureIntervalGroups extends Migration
{
    const GROUPS = [
        [
            'key' => 'EGBB_SID',
            'description' => 'All Birmingham Departures'
        ],
        [
            'key' => 'EGCC_SANBA_23',
            'description' => 'EGCC SANBA Runway 23',
        ],
        [
            'key' => 'EGCC_LISTO_23',
            'description' => 'EGCC LISTO Runway 23',
        ],
        [
            'key' => 'EGCC_NORTH_EAST_23',
            'description' => 'EGCC North and Eastbound Departures Runway 23',
        ],
        [
            'key' => 'EGCC_EKLAD_KUXEM_23',
            'description' => 'EGCC EKLAD and KUXEM Departures Runway 23',
        ],
        [
            'key' => 'EGCC_MONTY_23',
            'description' => 'EGCC MONTY Departures Runway 23',
        ],
        [
            'key' => 'EGCC_LISTO_05',
            'description' => 'EGCC LISTO Departures On Runway 05',
        ],
        [
            'key' => 'EGCC_MONTY_05',
            'description' => 'EGCC MONTY Departures On Runway 05',
        ],
        [
            'key' => 'EGCC_ASMIM_05',
            'description' => 'EGCC ASMIM Departures On Runway 05',
        ],
        [
            'key' => 'EGCC_DESIG_05',
            'description' => 'EGCC DESIG Departures Runway 05',
        ],
        [
            'key' => 'EGCC_POL_05',
            'description' => 'EGCC DESIG Departures Runway 05',
        ],
        [
            'key' => 'EGCN_ROGAG_02',
            'description' => 'EGCN ROGAG Departures Runway 02',
        ],
        [
            'key' => 'EGCN_ROGAG_20',
            'description' => 'EGCN ROGAG Departures Runway 20',
        ],
        [
            'key' => 'EGCN_UPTON_20',
            'description' => 'EGCN UPTON Departures Runway 20',
        ],
        [
            'key' => 'EGCN_UPTON_02',
            'description' => 'EGCN UPTON Departures Runway 02',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $formattedGroups = [];
        foreach (self::GROUPS as $group) {
            $formattedGroups[] = array_merge(
                $group,
                ['created_at' => Carbon::now()],
            );
        }

        DB::table('sid_departure_interval_groups')->insert($formattedGroups);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sid_departure_interval_groups')->delete();
    }
}
