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
