<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateMilitaryPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Delete the old positions
        DB::table('controller_positions')
            ->whereIn('callsign', ['EGVV_L_CTR', 'EGWD_CTR'])
            ->delete();

        // Add the new ones
        DB::table('controller_positions')
            ->insert(
                [
                    [
                        'callsign' => 'BLKDG_CTR',
                        'frequency' => 133.32,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'BLKDG_APP',
                        'frequency' => 133.32,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'HTSPR_CTR',
                        'frequency' => 135.07,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'HTSPR_APP',
                        'frequency' => 135.07,
                        'created_at' => Carbon::now(),
                    ],
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
        // Delete the new positions
        DB::table('controller_positions')
            ->whereIn('callsign', ['BLKDG_CTR', 'BLKDG_APP', 'HTSPR_CTR', 'HTSPR_APP'])
            ->delete();

        // Add the old ones
        DB::table('controller_positions')
            ->insert(
                [
                    [
                        'callsign' => 'EGVV_L_CTR',
                        'frequency' => 127.45,
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'callsign' => 'EGWD_CTR',
                        'frequency' => 135.27,
                        'created_at' => Carbon::now(),
                    ],
                ]
            );

        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
    }
}
