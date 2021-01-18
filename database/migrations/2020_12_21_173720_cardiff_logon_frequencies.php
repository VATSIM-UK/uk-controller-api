<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CardiffLogonFrequencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')
            ->where('callsign', 'EGFF_APP')
            ->update(['callsign' => 'EGFF_L_APP', 'updated_at' => Carbon::now()]);

        // For some reason, we have a dodgy callsign for the primary. Lets take this chance to tidy up.
        DB::table('controller_positions')
            ->where('callsign', 'EGFF_R_APP')
            ->update(['callsign' => 'EGFF_APP', 'updated_at' => Carbon::now()]);

        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
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
