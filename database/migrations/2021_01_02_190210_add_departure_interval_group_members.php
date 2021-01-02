<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDepartureIntervalGroupMembers extends Migration
{
    const GROUPS = [

    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::GROUPS as $group => $sids) {
            $groupId = DB::table('sid_departure_interval_groups')
                ->where('key', $group)
                ->first()
                ->id;

            DB::table('sid')->whereIn('identifier', $sids)
                ->update(['sid_departure_interval_group_id' => $groupId, 'updated_at' => Carbon::now()]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sid')->update(['sid_departure_interval_group_id' => null, 'updated_at' => Carbon::now()]);
    }
}
