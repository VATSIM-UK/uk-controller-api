<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SouthamptonStandardDepartures extends Migration
{
    const SIDS = [
        'NORRY',
        'KENET',
        'GWC',
        'NEDUL'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $southampton = DB::table('airfield')
            ->where('code', 'EGHI')
            ->first()
            ->id;

        // Create the handoff
        HandoffService::createNewHandoffOrder(
            'EGHI_DEPARTURE',
            'EGHI Departures',
            [
                'SOLENT_APP',
                'LON_S_CTR',
                'LON_SC_CTR',
                'LON_CTR',
            ]
        );

        // Create the SIDs
        foreach (self::SIDS as $sid) {
            DB::table('sid')->insert(
                [
                    'airfield_id' => $southampton,
                    'identifier' => $sid,
                    'initial_altitude' => 3000,
                    'created_at' => Carbon::now(),
                ]
            );

            HandoffService::setHandoffForSid('EGHI', $sid, 'EGHI_DEPARTURE');
        }

        // Update dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID');
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
