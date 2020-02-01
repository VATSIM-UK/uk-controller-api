<?php

use App\Models\Controller\ControllerPosition;
use App\Services\AirfieldService;
use App\Services\HandoffService;
use App\Services\PrenoteService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Airac202002DaventrySplits extends Migration
{
    const POSITION_DATA = [
        [
            'callsign' => 'LON_CE_CTR',
            'frequency' => 127.87,
            'top_down' => [
                'EGNX' => 'LON_C_CTR',
                'EGTC' => 'LON_C_CTR',
                'EGSS' => 'LON_C_CTR',
                'EGGW' => 'LON_C_CTR',
                'EGSC' => 'LON_C_CTR',
            ],
            'handoff' => [
                'EGNX_SID_NORTH_09' => 'LON_C_CTR',
                'EGNX_SID_SOUTH_09' => 'LON_C_CTR',
                'EGNX_SID_SOUTH_27' => 'LON_C_CTR',
                'EGSS_SID_WEST' => 'LON_C_CTR',
                'EGGW_SID_WEST_08' => 'LON_C_CTR',
                'EGGW_SID_WEST_26' => 'LON_C_CTR',
            ],
            'prenote' => [
                'EGSS_SID_NUGBO' => 'LON_C_CTR',
                'PAIRING_ESSEX_LTMA_NORTH_WEST' => 'LON_C_CTR',
            ]
        ],
        [
            'callsign' => 'LON_CW_CTR',
            'frequency' => 134.4,
            'top_down' => [
                'EGBB' => 'LON_C_CTR',
                'EGTK' => 'LON_C_CTR',
            ],
            'handoff' => [
                'EGBB_SID' => 'LON_C_CTR',
            ]
        ],
        [
            'callsign' => 'LON_CL_CTR',
            'frequency' => 133.97,
            'top_down' => [
                'EGNX' => 'LON_CE_CTR',
                'EGTC' => 'LON_CE_CTR',
                'EGSS' => 'LON_CE_CTR',
                'EGGW' => 'LON_CE_CTR',
                'EGSC' => 'LON_CE_CTR',
            ],
            'handoff' => [
                'EGNX_SID_NORTH_09' => 'LON_CE_CTR',
                'EGNX_SID_SOUTH_09' => 'LON_CE_CTR',
                'EGNX_SID_SOUTH_27' => 'LON_CE_CTR',
                'EGSS_SID_WEST' => 'LON_CE_CTR',
                'EGGW_SID_WEST_08' => 'LON_CE_CTR',
                'EGGW_SID_WEST_26' => 'LON_CE_CTR',
            ],
            'prenote' => [
                'EGSS_SID_NUGBO' => 'LON_CE_CTR',
                'PAIRING_ESSEX_LTMA_NORTH_WEST' => 'LON_CE_CTR',
            ]
        ],
        [
            'callsign' => 'LTC_M_CTR',
            'frequency' => 121.02,
            'top_down' => [
                'EGNX' => 'LON_CL_CTR',
                'EGTC' => 'LON_CL_CTR',
                'EGBB' => 'LON_CW_CTR',
                'EGTK' => 'LON_CW_CTR',
            ],
            'handoff' => [
                'EGNX_SID_NORTH_09' => 'LON_CL_CTR',
                'EGNX_SID_SOUTH_09' => 'LON_CL_CTR',
                'EGNX_SID_SOUTH_27' => 'LON_CL_CTR',
                'EGBB_SID' => 'LON_CW_CTR',
            ],
        ],
        [
            'callsign' => 'LTC_MC_CTR',
            'frequency' => 121.02,
            'top_down' => [
                'EGBB' => 'LTC_M_CTR',
                'EGTK' => 'LTC_M_CTR',
            ],
            'handoff' => [
                'EGBB_SID' => 'LTC_M_CTR',
            ],
        ],
        [
            'callsign' => 'LTC_MW_CTR',
            'frequency' => 121.02,
            'top_down' => [
                'EGNX' => 'LTC_M_CTR',
                'EGTC' => 'LTC_M_CTR',
            ],
            'handoff' => [
                'EGNX_SID_NORTH_09' => 'LTC_M_CTR',
                'EGNX_SID_SOUTH_09' => 'LTC_M_CTR',
                'EGNX_SID_SOUTH_27' => 'LTC_M_CTR',
            ],
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::beginTransaction();
            foreach (self::POSITION_DATA as $position) {
                ControllerPosition::create(
                    [
                        'callsign' => $position['callsign'],
                        'frequency' => $position['frequency']
                    ]
                );

                foreach ($position['top_down'] as $airfield => $insertBefore) {
                    AirfieldService::insertIntoOrderBefore($airfield, $position['callsign'], $insertBefore);
                }

                foreach ($position['handoff'] as $handoff => $insertBefore) {
                    HandoffService::insertIntoOrderBefore($handoff, $position['callsign'], $insertBefore);
                }

                if (isset($position['prenote'])) {
                    foreach ($position['prenote'] as $prenote => $insertBefore) {
                        PrenoteService::insertIntoOrderBefore($prenote, $position['callsign'], $insertBefore);
                    }
                }
            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            DB::beginTransaction();
            foreach (self::POSITION_DATA as $position) {
                AirfieldService::removePositionFromAllTopDowns($position['callsign']);
                HandoffService::removePositionFromAllHandoffs($position['callsign']);
                PrenoteService::removePositionFromAllPrenotes($position['callsign']);
                ControllerPosition::where('callsign', $position['callsign'])->firstOrFail()->delete();
            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
        }
    }
}
