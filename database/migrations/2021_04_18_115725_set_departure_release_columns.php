<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SetDepartureReleaseColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')
            ->where('callsign', 'LIKE', '%_APP')
            ->orWhere('callsign', 'LIKE', '%_CTR')
            ->orWhere('callsign', 'LIKE', '%_TWR')
            ->update(['requests_departure_releases' => true, 'updated_at' => Carbon::now()]);

        DB::table('controller_positions')
            ->where('callsign', 'LIKE', '%_APP')
            ->orWhere('callsign', 'LIKE', '%_CTR')
            ->update(['receives_departure_releases' => true, 'updated_at' => Carbon::now()]);

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
