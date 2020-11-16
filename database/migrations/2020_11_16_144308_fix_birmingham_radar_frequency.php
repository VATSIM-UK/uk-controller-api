<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixBirminghamRadarFrequency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')
            ->where('callsign', 'EGBB_APP')
            ->update(['frequency' => '123.970']);
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('controller_positions')
            ->where('callsign', 'EGBB_APP')
            ->update(['frequency' => '123.950']);
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
    }
}
