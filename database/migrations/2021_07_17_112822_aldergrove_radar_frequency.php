<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AldergroveRadarFrequency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')
            ->where('callsign', 'EGAA_R_APP')
            ->update(['frequency' => '133.120', 'updated_at' => Carbon::now()]);
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS_V2');
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
