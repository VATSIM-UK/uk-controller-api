<?php

use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Airac202002AerodromeUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Frequency updates
        ControllerPosition::where('callsign', 'EGBB_TWR')->update(['frequency' => 123.97]);
        ControllerPosition::where('callsign', 'EGMC_TWR')->update(['frequency' => 127.75]);

        // Create LF GND position
        $lfGnd = ControllerPosition::create(
            [
                'callsign' => 'EGLF_GND',
                'frequency' => 121.82,
            ]
        );

        // Update LF top-down
        $lf = Airfield::where('code', 'EGLF')->firstOrFail();
        DB::table('top_downs')->where('airfield_id', $lf->id)->delete();
        DB::table('top_downs')->insert(
            [
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLF_GND')->firstOrFail()->id,
                    'order' => 1,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLF_TWR')->firstOrFail()->id,
                    'order' => 2,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLF_APP')->firstOrFail()->id,
                    'order' => 3,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SW_CTR')->firstOrFail()->id,
                    'order' => 4,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 5,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 6,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 7,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 8,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 9,
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
        ControllerPosition::where('callsign', 'EGBB_TWR')->update(['frequency' => 118.3]);
        ControllerPosition::where('callsign', 'EGMC_TWR')->update(['frequency' => 126.72]);

        // Delete LF GND
        ControllerPosition::where('callsign', 'EGLF_GND')->firstOrFail()->delete();

        // Update LF top-down
        $lf = Airfield::where('code', 'EGLF')->firstOrFail();
        DB::table('top_downs')->where('airfield_id', $lf->id)->delete();
        DB::table('top_downs')->insert(
            [
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLF_TWR')->firstOrFail()->id,
                    'order' => 1,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'EGLF_APP')->firstOrFail()->id,
                    'order' => 2,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_SW_CTR')->firstOrFail()->id,
                    'order' => 3,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_S_CTR')->firstOrFail()->id,
                    'order' => 4,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LTC_CTR')->firstOrFail()->id,
                    'order' => 5,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_S_CTR')->firstOrFail()->id,
                    'order' => 6,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_SC_CTR')->firstOrFail()->id,
                    'order' => 7,
                ],
                [
                    'airfield_id' => $lf->id,
                    'controller_position_id' => ControllerPosition::where('callsign', 'LON_CTR')->firstOrFail()->id,
                    'order' => 8,
                ],
            ]
        );
    }
}
