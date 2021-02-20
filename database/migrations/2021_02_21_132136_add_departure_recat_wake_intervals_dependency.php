<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDepartureRecatWakeIntervalsDependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dependencies')->insert(
            [
                'key' => 'DEPENDENCY_DEPARTURE_WAKE_RECAT',
                'action' => 'DepartureController@getDepartureRecatWakeIntervalsDependency',
                'local_file' => 'departure-recat-wake-intervals.json',
                'created_at' => Carbon::now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dependencies')->where('key', 'DEPENDENCY_DEPARTURE_WAKE_RECAT')->delete();
    }
}
