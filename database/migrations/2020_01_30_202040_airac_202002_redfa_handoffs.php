<?php

use App\Models\Controller\ControllerPosition;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Airac202002RedfaHandoffs extends Migration
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
            // Handoff orders - REDFA
            HandoffService::updateAllHandoffsWithPosition('LTC_E_CTR', 'LTC_ER_CTR', true);
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
        DB::beginTransaction();
        try {
            // Handoff orders - REDFA
            HandoffService::removePositionFromAllHandoffs('LTC_ER_CTR');
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
