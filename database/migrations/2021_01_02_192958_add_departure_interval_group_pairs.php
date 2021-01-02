<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDepartureIntervalGroupPairs extends Migration
{
    const PAIRS = [
        [
            'EGBB_SID',
            'EGBB_SID',
            120
        ],
        [
            'EGCC_WEST_23',
            'EGCC_WEST_23',
            120,
        ],
        [
            'EGCC_WEST_23',
            'EGCC_LISTO_23',
            60,
        ],
        [
            'EGCC_WEST_23',
            'EGCC_SANBA_23',
            120,
        ],
        [
            'EGCC_WEST_23',
            'EGCC_NORTH_EAST_23',
            60,
        ],
        [
            'EGCC_LISTO_23',
            'EGCC_WEST_23',
            60,
        ],
        [
            'EGCC_LISTO_23',
            'EGCC_LISTO_23',
            120,
        ],
        [
            'EGCC_LISTO_23',
            'EGCC_NORTH_EAST_23',
            120,
        ],
        [
            'EGCC_NORTH_EAST_23',
            'EGCC_NORTH_EAST_23',
            120,
        ],
        [
            'EGCC_NORTH_EAST_23',
            'EGCC_WEST_23',
            60,
        ],
        [
            'EGCC_NORTH_EAST_23',
            'EGCC_LISTO_23',
            60,
        ],
        [
            'EGCC_NORTH_EAST_23',
            'EGCC_SANBA_23',
            60,
        ],
        [
            'EGCC_WEST_05',
            'EGCC_WEST_05',
            120,
        ],
        [
            'EGCC_WEST_05',
            'EGCC_NORTH_EAST_05',
            60,
        ],
        [
            'EGCC_WEST_05',
            'EGCC_SOUTH_05',
            60,
        ],
        [
            'EGCC_NORTH_EAST_05',
            'EGCC_WEST_05',
            60,
        ],
        [
            'EGCC_NORTH_EAST_05',
            'EGCC_SOUTH_05',
            60,
        ],
        [
            'EGCC_NORTH_EAST_05',
            'EGCC_NORTH_EAST_05',
            120,
        ],
        [
            'EGCC_SOUTH_05',
            'EGCC_WEST_05',
            60,
        ],
        [
            'EGCC_SOUTH_05',
            'EGCC_NORTH_EAST_05',
            60,
        ],
        [
            'EGCC_SOUTH_05',
            'EGCC_SOUTH_05',
            120,
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $groups = DB::table('sid_departure_interval_groups')->get()->mapWithKeys(
            function ($group) {
                return [$group->key => $group->id];
            }
        );

        foreach (self::PAIRS as $pair) {
            DB::table('sid_departure_interval_group_sid_departure_interval_group')
                ->insert(
                    [
                        'lead_group_id' =>  $groups[$pair[0]],
                        'follow_group_id' =>  $groups[$pair[1]],
                        'interval' =>  $pair[2],
                    ]
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
        DB::table('sid_departure_interval_group_sid_departure_interval_group')->delete();
    }
}
