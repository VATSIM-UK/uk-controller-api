<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SetAirlineStandPriorities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('airline_stand')
            ->join('stands', 'airline_stand.stand_id', '=', 'stands.id')
            ->update(['airline_stand.priority' => DB::raw('`stands`.`assignment_priority`')]);
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
