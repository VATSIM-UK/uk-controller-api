<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddBrizeDirectorPosition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')->insert(
            [
                'callsign' => 'EGVN_F_APP',
                'frequency' => 133.75,
                'created_at' => Carbon::now(),
            ]
        );

        DB::table('controller_positions')
            ->where('callsign', 'EGVN_R_APP')
            ->update(['callsign' => 'EGVN_APP']);

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
            ->where('callsign', 'EGVN_F_APP')
            ->delete();

        DB::table('controller_positions')
            ->where('callsign', 'EGVN_APP')
            ->update(['callsign' => 'EGVN_R_APP']);

        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
    }
}
