<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateHeathrowSidHandoffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update the handoff description
        DB::table('handoffs')
            ->where('key', 'EGLL_SID_SOUTH_WEST')
            ->update(['description' => 'Heathrow South-westbound Departures']);

        // Update the handoffs for the Heathrow SIDs
        $handoffId = DB::table('handoffs')
            ->where('key', 'EGLL_SID_SOUTH_WEST')
            ->select('id')
            ->first()
            ->id;

        DB::table('sid')
            ->whereIn('identifier', ['GASGU2K', 'GASGU2J', 'GOGSI2G', 'GOGSI2F'])
            ->update(['handoff_id' => $handoffId]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // None
    }
}
