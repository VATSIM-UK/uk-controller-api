<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ScottishTmaHandoffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // EGPH - Add SCO_S To South Departures
        HandoffService::insertIntoOrderBefore('EGPH_SID_SOUTH', 'SCO_S_CTR', 'SCO_CTR');

        // EGPH - Add SCO_S To North Departures
        HandoffService::insertIntoOrderBefore('EGPH_SID_NORTH', 'SCO_S_CTR', 'SCO_E_CTR');

        // EGPH - Add New Order for TLA
        HandoffService::createNewHandoffOrder(
            'EGPH_SID_EAST',
            'EGPH Eastbound SIDs (TLA)',
            [
                'STC_E_CTR',
                'STC_CTR',
                'SCO_D_CTR',
                'SCO_WD_CTR',
                'SCO_S_CTR',
                'SCO_CTR',
                'EGPH_APP',
            ]
        );

        DB::table('sid')->whereIn(
            'identifier',
            ['TLA6C', 'TLA6D']
        )
            ->update(['handoff_id' => DB::table('handoffs')->where(['key' => 'EGPH_SID_EAST'])->first()->id]);

        // EGPF - Add SCO_S To South Departures
        HandoffService::insertIntoOrderBefore('EGPF_SID_SOUTH', 'SCO_S_CTR', 'SCO_CTR');

        // EGPF - Add New LUSIV/TRN Handoff For Runway 23
        HandoffService::createNewHandoffOrder(
            'EGPH_SID_TRN_LUSIV_23',
            'EGPH Departures via TRN/LUSIV Runway 23',
            [
                'STC_W_CTR',
                'STC_CTR',
                'SCO_D_CTR',
                'SCO_WD_CTR',
                'SCO_S_CTR',
                'SCO_CTR',
                'EGPF_APP',
            ]
        );

        DB::table('sid')->whereIn(
            'identifier',
            ['LUSIV1A', 'TRN3A']
        )
            ->update(['handoff_id' => DB::table('handoffs')->where(['key' => 'EGPH_SID_TRN_LUSIV_23'])->first()->id]);

        // EGPF - Add New LUSIV/TRN Handoff For Runway 05
        HandoffService::createNewHandoffOrder(
            'EGPH_SID_TRN_LUSIV_05',
            'EGPH Departures via TRN/LUSIV Runway 05',
            [
                'EGPF_APP',
                'STC_W_CTR',
                'STC_CTR',
                'SCO_D_CTR',
                'SCO_WD_CTR',
                'SCO_S_CTR',
                'SCO_CTR',
            ]
        );

        DB::table('sid')->whereIn(
            'identifier',
            ['LUSIV1B', 'TRN6B']
        )
            ->update(['handoff_id' => DB::table('handoffs')->where(['key' => 'EGPH_SID_TRN_LUSIV_05'])->first()->id]);

        // Add SCO_S To EGPF Northbound Departures
        HandoffService::insertIntoOrderBefore('EGPF_SID_NORTH', 'SCO_S_CTR', 'SCO_E_CTR');

        // Rename EGPK_SID to EGPK_SID_EAST_SOUTH
        DB::table('handoffs')
            ->where('key', 'EGPK_SID')
            ->update(['key' => 'EGPK_SID_EAST_SOUTH', 'description' => 'EGPK SIDs Eastbound and TRN']);

        // Add SCO_S To EGPK Westbound Departures
        HandoffService::insertIntoOrderBefore('EGPK_SID_EAST_SOUTH', 'SCO_S_CTR', 'SCO_CTR');

        // Add missing EGPK SID OKNOB1L
        DB::table('sid')->insert(
            [
                'airfield_id' => DB::table('airfield')->where('code', 'EGPK')->first()->id,
                'identifier' => 'OKNOB1L',
                'initial_altitude' => 6000,
                'created_at' => Carbon::now(),
            ]
        );

        // Create new handoff EGPK_SID_WEST
        HandoffService::createNewHandoffOrder(
            'EGPK_SID_WEST',
            'EGPK Westbound SIDs',
            [
                'SCO_R_CTR',
                'SCO_W_CTR',
                'SCO_WD_CTR',
                'SCO_CTR',
                'EGPK_APP'
            ]
        );

        DB::table('sid')->whereIn(
            'identifier',
            ['DAUNT1K', 'OKNOB1L']
        )
            ->update(['handoff_id' => DB::table('handoffs')->where(['key' => 'EGPK_SID_WEST'])->first()->id]);

        // Do the dependencies
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
        // Nothing to do
    }
}
