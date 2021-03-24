<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddHawardenAirwaysDepartures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the new SIDs
        $hawarden = DB::table('airfield')->where('code', 'EGNR')->first()->id;
        DB::table('sid')->insert(
            [
                [
                    'airfield_id' => $hawarden,
                    'identifier' => 'REXAM5',
                    'initial_altitude' => 5000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $hawarden,
                    'identifier' => 'WAL4',
                    'initial_altitude' => 4000,
                    'created_at' => Carbon::now(),
                ],
            ]
        );

        HandoffService::createNewHandoffOrder(
            'EGNR_DEPARTURE',
            'EGNR Airways Departures',
            [
                'MAN_WL_CTR',
                'MAN_WP_CTR',
                'MAN_W_CTR',
                'MAN_CTR',
                'LON_N_CTR',
                'LON_CTR',
                'EGGP_APP',
                'EGNR_APP'
            ]
        );
        HandoffService::setHandoffForSid('EGNR', 'REXAM5', 'EGNR_DEPARTURE');
        HandoffService::setHandoffForSid('EGNR', 'WAL4', 'EGNR_DEPARTURE');

        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sid')->whereIn('identifier', ['REXAM5', 'WAL4'])->delete();
        HandoffService::deleteHandoffByKey('EGNR_DEPARTURE');
        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
    }
}
