<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CloseEdinburghStands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('stands')
            ->where('airfield_id', DB::table('airfield')->where('code', 'EGPH')->first()->id)
            ->whereIn('identifier', ['9A', '10A', '209'])
            ->update(['closed_at' => Carbon::now()]);
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
