<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddBrizeLarsPosition extends Migration
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
                'callsign' => 'EGVN_L_APP',
                'frequency' => 124.27,
                'created_at' => Carbon::now(),
            ]
        );
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
            ->where('callsign', 'EGVN_L_APP')
            ->delete();
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
    }
}
