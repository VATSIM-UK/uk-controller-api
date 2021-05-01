<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveUnusedDependencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dependencies')
            ->whereIn('key', ['DEPENDENCY_DEPARTURE_WAKE_RECAT', 'DEPENDENCY_DEPARTURE_WAKE_UK'])
            ->delete();
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
