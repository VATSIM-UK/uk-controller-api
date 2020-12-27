<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddLeedsHold extends Migration
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
                'identifier' => 'LBA',
                'latitude' => 'N053.51.53.970',
                'longitude' => 'W001.39.10.410',
                'created_at' => Carbon::now(),
            ]
        );

        DB::table('holds')->insert(
            [
                'navaid_id' => $navaidId,
                'inbound_heading' => 139,
                'minimum_altitude' => 8000,
                'maximum_altitude' => 8000,
                'turn_direction' => 'left',
                'description' => 'LBA - Leeds',
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
        DB::table('navaids')->where('identifier', 'LBA')->delete();
        DependencyService::touchDependencyByKey('DEPENDENCY_NAVAIDS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
    }
}
