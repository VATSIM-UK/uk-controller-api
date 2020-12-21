<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NewquayStandardDepartureRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        HandoffService::createNewHandoffOrder(
            'EGHQ_DEPARTURE',
            'EGHQ Standard Departures',
            [
                'EGHQ_APP',
                'LON_W_CTR',
                'LON_CTR'
            ]
        );

        $handoffId = DB::table('handoffs')->where('key', 'EGHQ_DEPARTURE')->first()->id;
        $newquayId = DB::table('airfield')->where('code', 'EGHQ')->first()->id;

        DB::table('sid')->insert(
            [
                [
                    'airfield_id' => $newquayId,
                    'identifier' => 'DAWLY',
                    'initial_altitude' => 19000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $newquayId,
                    'identifier' => 'EXMOR',
                    'initial_altitude' => 19000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $newquayId,
                    'identifier' => 'STU',
                    'initial_altitude' => 19000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $newquayId,
                    'identifier' => 'LND',
                    'initial_altitude' => 19000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $newquayId,
                    'identifier' => 'BHD',
                    'initial_altitude' => 19000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
            ]
        );

        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
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
