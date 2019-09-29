<?php

use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddHandoffOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->getHandoffOrderData() as $key => $data) {
            Handoff::where('key', $key)
                ->firstOrFail()
                ->controllers()
                ->attach($data);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $handoffs = Handoff::all();
        $handoffs->each(function (Handoff $handoff) {
            $handoff->controllers()->detach($handoff->controllers->pluck('id')->toArray());
        });
    }

    private function getHandoffOrderData()
    {
        return [
            // EGKK
            'EGKK_SID_EAST' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SE_CTR')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_D_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGKK_APP')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGKK_SID_BIG' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SE_CTR')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_D_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLL_S_APP')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLL_N_APP')->firstOrFail()->id,
                    'order' => 9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGKK_APP')->firstOrFail()->id,
                    'order' => 10,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGKK_SID_WEST' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SW_CTR')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGKK_APP')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGKK_SID_SFD_08' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGKK_F_APP')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGKK_APP')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SE_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_D_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 9,
                    'created_at' => Carbon::now(),
                ],
            ],

            // EGLL
            'EGLL_SID_SOUTH_EAST' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SE_CTR')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_D_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLL_S_APP')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLL_N_APP')->firstOrFail()->id,
                    'order' => 9,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGLL_SID_SOUTH_WEST' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SW_CTR')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLL_S_APP')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLL_N_APP')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGLL_SID_NORTH_WEST' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_NW_CTR')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_N_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_C_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLL_N_APP')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGLL_SID_NORTH_EAST' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_NE_CTR')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_N_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_E_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_E_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_C_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLL_N_APP')->firstOrFail()->id,
                    'order' => 9,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGLL_SID_CPT_09' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLL_S_APP')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLL_N_APP')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SW_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
            ],

            // EGLC
            'EGLC_SID_SOUTH' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'THAMES_APP')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SE_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_D_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGLC_SID_CLN' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'THAMES_APP')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SE_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_D_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_NE_CTR')->firstOrFail()->id,
                    'order' => 9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_N_CTR')->firstOrFail()->id,
                    'order' => 10,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_E_CTR')->firstOrFail()->id,
                    'order' => 11,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_E_CTR')->firstOrFail()->id,
                    'order' => 12,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_C_CTR')->firstOrFail()->id,
                    'order' => 13,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGLC_SID_BPK_CPT_09' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_NE_CTR')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_N_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_E_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_E_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_C_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'THAMES_APP')->firstOrFail()->id,
                    'order' => 9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SE_CTR')->firstOrFail()->id,
                    'order' => 10,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 11,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_D_CTR')->firstOrFail()->id,
                    'order' => 12,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 13,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGLC_SID_BPK_CPT_27' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'THAMES_APP')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_NE_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_N_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_E_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_E_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_C_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SE_CTR')->firstOrFail()->id,
                    'order' => 10,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 11,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_D_CTR')->firstOrFail()->id,
                    'order' => 12,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 13,
                    'created_at' => Carbon::now(),
                ],
            ],

            // EGSS
            'EGSS_SID_WEST' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGSS_APP')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'ESSEX_APP')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_NW_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_N_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_C_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGSS_SID_EAST_SOUTH' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_NE_CTR')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_N_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_E_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_E_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_C_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGSS_APP')->firstOrFail()->id,
                    'order' => 9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'ESSEX_APP')->firstOrFail()->id,
                    'order' => 10,
                    'created_at' => Carbon::now(),
                ],
            ],

            // EGGW
            'EGGW_SID_SOUTH_EAST' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_NE_CTR')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_N_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_E_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_E_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_C_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'ESSEX_APP')->firstOrFail()->id,
                    'order' => 9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGGW_APP')->firstOrFail()->id,
                    'order' => 10,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGGW_SID_WEST_26' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_NW_CTR')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_N_CTR')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_C_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'ESSEX_APP')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGGW_APP')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
            ],
            'EGGW_SID_WEST_08' => [
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGGW_APP')->firstOrFail()->id,
                    'order' => 1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'ESSEX_APP')->firstOrFail()->id,
                    'order' => 2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_NW_CTR')->firstOrFail()->id,
                    'order' => 3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_N_CTR')->firstOrFail()->id,
                    'order' => 4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_C_CTR')->firstOrFail()->id,
                    'order' => 6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 8,
                    'created_at' => Carbon::now(),
                ],
            ],
        ];
    }
}
