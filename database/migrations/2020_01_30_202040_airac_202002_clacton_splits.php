<?php

use App\Models\Controller\ControllerPosition;
use App\Services\HandoffService;
use App\Services\PrenoteService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Airac202002ClactonSplits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();

        try {
            // Add the positions
            ControllerPosition::create(
                [
                    'callsign' => 'LON_EN_CTR',
                    'frequency' => 133.95
                ]
            );

            ControllerPosition::create(
                [
                    'callsign' => 'LON_ES_CTR',
                    'frequency' => 133.95
                ]
            );

            // Handoff orders
            HandoffService::updateAllHandoffsWithPosition('LON_E_CTR', 'LON_EN_CTR', true);

            // Prenotes
            PrenoteService::updateAllPrenotesWithPosition('LON_E_CTR', 'LON_EN_CTR', true);
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::beginTransaction();
        try {
            // Remove handoff orders
            HandoffService::removePositionFromAllHandoffs('LON_EN_CTR');

            // Remove prenote orders
            PrenoteService::removePositionFromAllPrenotes('LON_EN_CTR');

            // Delete the positions
            ControllerPosition::where('callsign', 'LON_EN_CTR')->firstOrFail()->delete();
            ControllerPosition::where('callsign', 'LON_ES_CTR')->firstOrFail()->delete();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
    }
}
