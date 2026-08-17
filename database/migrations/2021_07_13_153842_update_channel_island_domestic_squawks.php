<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateChannelIslandDomesticSquawks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('airfield_pairing_squawk_ranges')
            ->whereIn('origin', ['EGJA', 'EGJB', 'EGJJ'])
            ->where('destination', 'EG')
            ->update(['last' => '1247', 'updated_at' => Carbon::now()]);
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
