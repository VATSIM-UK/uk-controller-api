<?php

use App\Models\Controller\Handoff;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSouthendPdrs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the handoff orders
        HandoffService::createNewHandoffOrder(
            'EGMC_PDR_CLACTON',
            'Southend Clacton PDRs',
            [
                'EGMC_APP',
                'THAMES_APP',
                'LTC_SE_CTR',
                'LTC_S_CTR',
                'LTC_CTR',
                'LON_D_CTR',
                'LON_S_CTR',
                'LON_SC_CTR',
                'LON_CTR',
                'LTC_ER_CTR',
                'LTC_E_CTR',
                'LON_EN_CTR',
                'LON_E_CTR',
                'LON_C_CTR',
            ]
        );

        HandoffService::createNewHandoffOrder(
            'EGMC_PDR_EVNAS',
            'Southend EVNAS PDRs',
            [
                'EGMC_APP',
                'THAMES_APP',
                'LTC_SE_CTR',
                'LTC_S_CTR',
                'LTC_CTR',
                'LON_D_CTR',
                'LON_S_CTR',
                'LON_SC_CTR',
                'LON_CTR',
                'LTC_NE_CTR',
                'LTC_N_CTR',
                'LTC_E_CTR',
                'LON_EN_CTR',
                'LON_C_CTR',
            ]
        );

        HandoffService::createNewHandoffOrder(
            'EGMC_PDR_DET',
            'Southend Detling PDRs',
            [
                'EGMC_APP',
                'THAMES_APP',
                'LTC_SE_CTR',
                'LTC_S_CTR',
                'LTC_CTR',
                'LON_D_CTR',
                'LON_S_CTR',
                'LON_SC_CTR',
                'LON_CTR',
            ]
        );

        $handoffClacton = Handoff::where('key', 'EGMC_PDR_CLACTON')->firstOrFail();
        $handoffEvnas = Handoff::where('key', 'EGMC_PDR_EVNAS')->firstOrFail();
        $handoffDet = Handoff::where('key', 'EGMC_PDR_DET')->firstOrFail();

        // Create the SIDs
        $southend = DB::table('airfield')->where('code', 'EGMC')->pluck('id')->first();
        DB::table('sid')->insert(
            [
                [
                    'identifier' => 'PDRCPT',
                    'airfield_id' => $southend,
                    'initial_altitude' => 4000,
                    'handoff_id' => $handoffEvnas->id,
                    'created_at' => Carbon::now(),
                ],
                [
                    'identifier' => 'PDRBPK',
                    'airfield_id' => $southend,
                    'initial_altitude' => 4000,
                    'handoff_id' => $handoffEvnas->id,
                    'created_at' => Carbon::now(),
                ],
                [
                    'identifier' => 'PDRCLN',
                    'airfield_id' => $southend,
                    'initial_altitude' => 3000,
                    'handoff_id' => $handoffClacton->id,
                    'created_at' => Carbon::now(),
                ],
                [
                    'identifier' => 'PDRDVR',
                    'airfield_id' => $southend,
                    'initial_altitude' => 3000,
                    'handoff_id' => $handoffDet->id,
                    'created_at' => Carbon::now(),
                ],
                [
                    'identifier' => 'PDRLYD',
                    'airfield_id' => $southend,
                    'initial_altitude' => 3000,
                    'handoff_id' => $handoffDet->id,
                    'created_at' => Carbon::now(),
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $southend = DB::table('airfield')->where('code', 'EGMC')->pluck('id')->first();
        DB::table('sid')->where('airfield_id', $southend)->delete();
        DB::table('handoffs')->whereIn('key', ['EGMC_PDR_CLACTON', 'EGMC_PDR'])->delete();
    }
}
