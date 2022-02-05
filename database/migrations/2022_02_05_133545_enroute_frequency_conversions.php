<?php

use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Migrations\Migration;

class EnrouteFrequencyConversions extends Migration
{
    const FREQUENCY_CHANGES = [
        // LON AC
        'LON_CTR' => 127.825,
        'LON_W_CTR' => 126.075,
        'LON_M_CTR' => 120.025,
        'LON_E_CTR' => 118.475,
        'LON_S_CTR' => 129.425,

        // LON TC
        'LTC_N_CTR' => 119.775,
        'LTC_NW_CTR' => 121.275,
        'LTC_NE_CTR' => 118.825,
        'LTC_S_CTR' => 134.125,
        'LTC_SW_CTR' => 133.175,
        'LTC_SE_CTR' => 120.525,
        'LTC_E_CTR' => 121.225,

        // MPC
        'MAN_SE_CTR' => 134.425,

        // SCO AC
        'SCO_CTR' => 135.525,
        'SCO_E_CTR' => 121.325,
        'SCO_N_CTR' => 129.225,
        'SCO_S_CTR' => 134.775,
        'SCO_WD_CTR' => 133.875,
        'SCO_W_CTR' => 132.725,

        // SCO TC
        'STC_A_CTR' => 123.775,
        'STC_W_CTR' => 124.825,
        'STC_E_CTR' => 130.975,

        // Military
        'EGVV_N_CTR' => 136.375,
        'EGVV_E_CTR' => 133.325,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::FREQUENCY_CHANGES as $callsign => $frequency) {
            ControllerPosition::where('callsign', $callsign)->firstOrFail()->update(['frequency' => $frequency]);
        }
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
