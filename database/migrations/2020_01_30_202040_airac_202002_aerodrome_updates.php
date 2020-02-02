<?php

use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use App\Services\AirfieldService;
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
        try {
            DB::beginTransaction();
            // Frequency updates
            ControllerPosition::where('callsign', 'EGBB_TWR')->update(['frequency' => 123.97]);
            ControllerPosition::where('callsign', 'EGMC_TWR')->update(['frequency' => 127.75]);

            // Create LF GND position
            ControllerPosition::create(
                [
                    'callsign' => 'EGLF_GND',
                    'frequency' => 121.82,
                ]
            );

            // Update LF top-down
            AirfieldService::insertIntoOrderBefore('EGLF', 'EGLF_GND', 'EGLF_TWR');
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
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
            ControllerPosition::where('callsign', 'EGBB_TWR')->update(['frequency' => 118.3]);
            ControllerPosition::where('callsign', 'EGMC_TWR')->update(['frequency' => 126.72]);

            // Update LF top-down
            AirfieldService::removeFromTopDownsOrder('EGLF', 'EGLF_GND');

            // Delete LF GND
            ControllerPosition::where('callsign', 'EGLF_GND')->firstOrFail()->delete();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
