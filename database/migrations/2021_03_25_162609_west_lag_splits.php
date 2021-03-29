<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class WestLagSplits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Delete the old London West (North) position
        HandoffService::removePositionFromAllHandoffs('LON_WN_CTR');
        DB::table('controller_positions')->where('callsign', 'LON_WN_CTR')->delete();

        // North-side sectors

        // Pembroke
        $this->createControllerPosition('LON_WP_CTR', '133.220');
        $this->updateTopDownOrders(['EGGD', 'EGFF', 'EGSY', 'EGBJ'], 'LON_WP_CTR', 'LON_W_CTR');
        $this->updateHandoffOrders(
            [
                'EGGD_SID',
                'EGGD_SID_BCN_EXMOR_27',
                'EGFF_SID_NORTH',
                'EGFF_SID_SOUTH',
            ],
            'LON_WP_CTR',
            'LON_W_CTR'
        );

        // Brecon
        $this->createControllerPosition('LON_WB_CTR', '133.600');
        $this->updateTopDownOrders(['EGGD', 'EGFF', 'EGSY', 'EGBJ'], 'LON_WB_CTR', 'LON_WP_CTR');
        $this->updateHandoffOrders(
            [
                'EGGD_SID',
                'EGGD_SID_BCN_EXMOR_27',
                'EGFF_SID_NORTH',
                'EGFF_SID_SOUTH',
            ],
            'LON_WB_CTR',
            'LON_WP_CTR'
        );

        // Bristol
        $this->createControllerPosition('LON_WR_CTR', '134.750');
        $this->updateTopDownOrders(['EGGD', 'EGFF', 'EGSY', 'EGBJ'], 'LON_WR_CTR', 'LON_WB_CTR');
        $this->updateHandoffOrders(
            [
                'EGGD_SID',
                'EGGD_SID_BCN_EXMOR_27',
                'EGFF_SID_NORTH',
                'EGFF_SID_SOUTH',
            ],
            'LON_WR_CTR',
            'LON_WB_CTR'
        );

        // Strumble
        $this->createControllerPosition('LON_WT_CTR', '129.370');

        // South-side sectors

        // Exmoor
        $this->createControllerPosition('LON_WX_CTR', '128.820');
        $this->updateTopDownOrders(['EGHQ', 'EGHC', 'EGHE', 'EGTE', 'EGJA', 'EGJB', 'EGJJ'], 'LON_WX_CTR', 'LON_W_CTR');
        $this->updateHandoffOrders(
            [
                'EGHQ_DEPARTURE',
                'EGJB_SID',
                'EGJJ_SID',
            ],
            'LON_WX_CTR',
            'LON_W_CTR'
        );

        // Lands End
        $this->createControllerPosition('LON_WL_CTR', '132.950');
        $this->updateTopDownOrders(['EGHQ', 'EGHC', 'EGHE'], 'LON_WL_CTR', 'LON_WX_CTR');
        $this->updateHandoffOrders(
            [
                'EGHQ_DEPARTURE',
            ],
            'LON_WL_CTR',
            'LON_WX_CTR'
        );

        // Berry Head
        $this->createControllerPosition('LON_WH_CTR', '127.700');
        $this->updateTopDownOrders(['EGTE', 'EGJA', 'EGJB', 'EGJJ'], 'LON_WH_CTR', 'LON_WX_CTR');
        $this->updateHandoffOrders(
            [
                'EGJB_SID',
                'EGJJ_SID',
            ],
            'LON_WH_CTR',
            'LON_WX_CTR'
        );

        // Touch dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No return
    }

    private function createControllerPosition(string $callsign, string $frequency): void
    {
        DB::table('controller_positions')->insert(
            [
                'callsign' => $callsign,
                'frequency' => $frequency,
                'created_at' => Carbon::now(),
            ]
        );
    }

    private function updateTopDownOrders(
        array $airfields,
        string $callsignToInsert,
        string $callsignToInsertBefore
    ): void {
        foreach ($airfields as $airfield) {
            AirfieldService::insertIntoOrderBefore($airfield, $callsignToInsert, $callsignToInsertBefore);
        }
    }

    private function updateHandoffOrders(
        array $handoffs,
        string $callsignToInsert,
        string $callsignToInsertBefore
    ): void {
        foreach ($handoffs as $handoff) {
            HandoffService::insertIntoOrderBefore($handoff, $callsignToInsert, $callsignToInsertBefore);
        }
    }
}
