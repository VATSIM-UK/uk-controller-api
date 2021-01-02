<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDepartureIntervalGroupPairs extends Migration
{
    const PAIRS = [
        [
        ]
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $groups = DB::table('departure_interval_groups')->get()->mapWithKeys(
            function ($group) {
                return [$group->key => $group->id];
            }
        );

        foreach (self::PAIRS as $pair) {
            DB::table('departure_interval_group_departure_interval_group')
                ->insert(
                    [
                        'lead_group_id' =>  $groups[$pair[0]],
                        'follow_group_id' =>  $groups[$pair[1]],
                        'interval' =>  $groups[$pair[2]],
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
        DB::table('departure_interval_group_departure_interval_group')->delete();
    }
}
