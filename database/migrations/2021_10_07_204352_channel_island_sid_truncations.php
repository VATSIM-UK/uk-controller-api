<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChannelIslandSidTruncations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add them
        $jersey = DB::table('airfield')->where('code', 'EGJJ')->first()->id;
        $guernsey = DB::table('airfield')->where('code', 'EGJB')->first()->id;
        DB::table('sid')->insert(
            [
                [
                    'airfield_id' => $jersey,
                    'identifier' => 'LUSIT1A',
                    'initial_altitude' => 6000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $jersey,
                    'identifier' => 'LUSIT1B',
                    'initial_altitude' => 6000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $guernsey,
                    'identifier' => 'LUSIT1W',
                    'initial_altitude' => 6000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $guernsey,
                    'identifier' => 'LUSIT1E',
                    'initial_altitude' => 6000,
                    'created_at' => Carbon::now(),
                ],
            ]
        );

        // Handoffs
        HandoffService::setHandoffForSid('EGJJ', 'LUSIT1A', 'EGJJ_SID');
        HandoffService::setHandoffForSid('EGJJ', 'LUSIT1B', 'EGJJ_SID');
        HandoffService::setHandoffForSid('EGJB', 'LUSIT1W', 'EGJB_SID');
        HandoffService::setHandoffForSid('EGJB', 'LUSIT1E', 'EGJB_SID');

        // Dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_SIDS');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
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
