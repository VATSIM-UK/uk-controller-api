<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CopyLegacyAssignmentsToCcamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $aircraft = DB::table('network_aircraft')->pluck('callsign')->toArray();
        $assignments = DB::table('squawk_allocation')
            ->whereIn('callsign', $aircraft)
            ->select(['callsign', 'squawk'])
            ->get()
            ->map(function ($item) {
                return [
                    'callsign' => $item->callsign,
                    'code' => $item->squawk,
                ];
            })
            ->toArray();

        DB::table('ccams_squawk_assignments')->insert($assignments);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Nothing to do here.
    }
}
