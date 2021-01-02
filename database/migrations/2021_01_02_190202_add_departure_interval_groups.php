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
            'key' => 'EGCC_WEST_23',
            'description' => 'EGCC Westbound Departures Runway 23',
        ],
        [
            'key' => 'EGCC_SOUTH_05',
            'description' => 'EGCC Southbound Departures On Runway 05',
        ],
        [
            'key' => 'EGCC_WEST_05',
            'description' => 'EGCC Westbound Departures On Runway 05',
        ],
        [
            'key' => 'EGCC_NORTH_EAST_05',
            'description' => 'EGCC North and Eastbound Departures Runway 05',
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
