<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class GuernseyRadarChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')
            ->where('callsign', 'EGJB_APP')
            ->update(['frequency' => 118.900, 'updated_at' => Carbon::now()]);

        DB::table('controller_positions')
            ->insert(
                [
                    'frequency' => 124.500,
                    'callsign' => 'EGJB_F_APP',
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
        //
    }
}
