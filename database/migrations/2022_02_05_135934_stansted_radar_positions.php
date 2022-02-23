<?php

use App\Models\Controller\ControllerPosition;
use App\Services\AirfieldService;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;

class StanstedRadarPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->updateStanstedDirectorCallsign();
        $this->addStanstedRadarPosition();
        $this->updateStanstedAndCambridgeTopDownOrder();
        $this->updateHandoffOrders();
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

    private function updateStanstedDirectorCallsign()
    {
        ControllerPosition::where('callsign', 'EGSS_APP')
            ->firstOrFail()
            ->update(['callsign' => 'EGSS_F_APP']);
    }

    private function addStanstedRadarPosition()
    {
        $essex = ControllerPosition::where('callsign', 'ESSEX_APP')
            ->firstOrFail();

        $stanstedRadar = $essex->replicate();
        $stanstedRadar->callsign = 'EGSS_APP';
        $stanstedRadar->save();
    }

    private function updateStanstedAndCambridgeTopDownOrder()
    {
        AirfieldService::insertIntoOrderBefore(
            'EGSS',
            'EGSS_APP',
            'ESSEX_APP'
        );

        AirfieldService::insertIntoOrderBefore(
            'EGSC',
            'EGSS_APP',
            'ESSEX_APP'
        );
    }

    private function updateHandoffOrders()
    {
        $this->updateHandoffOrder('EGSS_SID_WEST');
        $this->updateHandoffOrder('EGSS_SID_EAST_SOUTH');
        $this->updateHandoffOrder('AIRFIELD_EGSS_DEFAULT_HANDOFF');
        $this->updateHandoffOrder('AIRFIELD_EGSC_DEFAULT_HANDOFF');
    }

    private function updateHandoffOrder(string $key)
    {
        HandoffService::insertIntoOrderBefore($key, 'EGSS_APP', 'ESSEX_APP');
    }
}
