<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddExterInitialAltitudes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        HandoffService::createNewHandoffOrder(
            'EGTE_DEPARTURE',
            'EGTE Standard Departures',
            [
                'EGTE_N_APP',
                'LON_WH_CTR',
                'LON_WX_CTR',
                'LON_W_CTR',
                'LON_CTR',
            ]
        );

        $handoffId = DB::table('handoffs')->where('key', 'EGTE_DEPARTURE')->first()->id;
        $exeterId = DB::table('airfield')->where('code', 'EGTE')->first()->id;

        DB::table('sid')->insert(
            [
                [
                    'airfield_id' => $exeterId,
                    'identifier' => 'EXMOR',
                    'initial_altitude' => 6000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $exeterId,
                    'identifier' => 'GIBSO',
                    'initial_altitude' => 6000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $exeterId,
                    'identifier' => 'BHD',
                    'initial_altitude' => 6000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $exeterId,
                    'identifier' => 'DAWLY',
                    'initial_altitude' => 6000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $exeterId,
                    'identifier' => 'NOTRO',
                    'initial_altitude' => 6000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $exeterId,
                    'identifier' => 'LND',
                    'initial_altitude' => 6000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $exeterId,
                    'identifier' => 'STU',
                    'initial_altitude' => 6000,
                    'handoff_id' => $handoffId,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $exeterId,
                    'identifier' => 'AMMAN',
                    'initial_altitude' => 6000,
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
