<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameManchesterPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')
            ->where('callsign', 'MAN_U_CTR')
            ->update(
                [
                    'callsign' => 'MAN_WU_CTR',
                    'frequency' => 118.770,
                    'updated_at' => Carbon::now(),
                ]
            );

        DB::table('controller_positions')
            ->where('callsign', 'MAN_S_CTR')
            ->update(
                [
                    'callsign' => 'MAN_SE_CTR',
                    'updated_at' => Carbon::now(),
                ]
            );

        // Touch dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS_V2');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
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
