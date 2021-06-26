<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddLondonAreaNorthSplits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add LON_NU, upper North only.
        DB::table('controller_positions')
            ->insert(
                [
                    'callsign' => 'LON_NU_CTR',
                    'frequency' => 132.87,
                    'requests_departure_releases' => false,
                    'receives_departure_releases' => true,
                    'created_at' => Carbon::now(),
                ]
            );

        // Add AC North West (Lakes) split
        DB::table('controller_positions')
            ->insert(
                [
                    'callsign' => 'LON_NW_CTR',
                    'frequency' => 135.570,
                    'requests_departure_releases' => true,
                    'receives_departure_releases' => true,
                    'created_at' => Carbon::now(),
                ]
            );

        // Add to top-downs
        $this->addToTopdown('EGGP');
        $this->addToTopdown('EGNH');
        $this->addToTopdown('EGNR');
        $this->addToTopdown('EGNS');
        $this->addToTopdown('EGCC');
        $this->addToTopdown('EGNM');
        $this->addToTopdown('EGNJ');
        $this->addToTopdown('EGCN');
        $this->addToTopdown('EGNT');
        $this->addToTopdown('EGNV');

        // Add to the handoffs
        $this->addToHandoff('EGGP_SID');
        $this->addToHandoff('EGNR_DEPARTURE');
        $this->addToHandoff('EGCC_SID_EAST_NORTH');
        $this->addToHandoff('EGCC_SID_WEST');
        $this->addToHandoff('EGCC_SID_SOUTH');
        $this->addToHandoff('EGNM_SID');
        $this->addToHandoff('EGCN_SID');
        $this->addToHandoff('EGNT_SID');

        // Touch dependencies
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS');
        DependencyService::touchDependencyByKey('DEPENDENCY_CONTROLLER_POSITIONS_V2');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
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

    private function addToTopdown(string $airfield): void
    {
        AirfieldService::insertIntoOrderBefore(
            $airfield,
            'LON_NW_CTR',
            'LON_N_CTR'
        );
    }

    private function addToHandoff(string $handoffKey): void
    {
        HandoffService::insertIntoOrderBefore(
            $handoffKey,
            'LON_NW_CTR',
            'LON_N_CTR'
        );
    }
}
