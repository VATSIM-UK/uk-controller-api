<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddDelboHold extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $navaidId = DB::table('navaids')->insertGetId(
            [
                'identifier' => 'DELBO',
                'latitude' => 'N051.52.37.000',
                'longitude' => 'W001.16.24.000',
                'created_at' => Carbon::now(),
            ]
        );

        DB::table('holds')->insert(
            [
                'navaid_id' => $navaidId,
                'inbound_heading' => 153,
                'minimum_altitude' => 15000,
                'maximum_altitude' => 20000,
                'turn_direction' => 'right',
                'description' => 'DELBO',
                'created_at' => Carbon::now(),
            ]
        );

        DependencyService::touchDependencyByKey('DEPENDENCY_NAVAIDS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('navaids')->where('identifier', 'DELBO')->delete();
        DependencyService::touchDependencyByKey('DEPENDENCY_NAVAIDS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
    }
}
