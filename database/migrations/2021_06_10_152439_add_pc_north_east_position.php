<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPcNorthEastPosition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $position = DB::table('controller_positions')
            ->insert(
                [
                    'callsign' => 'MAN_NE_CTR',
                    'frequency' => 135.7,
                    'requests_departure_releases' => true,
                    'receives_departure_releases' => true,
                    'created_at' => Carbon::now(),
                ]
            );

        // Top-downs
        $this->addToTopdown('EGNT');
        $this->addToTopdown('EGNV');
        $this->addToTopdown('EGNM');
        $this->addToTopdown('EGNJ');
        $this->addToTopdown('EGCN');

        // Handoffs
        $this->addToHandoff('EGCC_SID_EAST_NORTH');
        $this->addToHandoff('EGNT_SID');
        $this->addToHandoff('EGNM_SID');
        $this->addToHandoff('EGCN_SID');

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
            'MAN_NE_CTR',
            'MAN_E_CTR'
        );
    }

    private function addToHandoff(string $handoffKey): void
    {
        HandoffService::insertIntoOrderBefore(
            $handoffKey,
            'MAN_NE_CTR',
            'MAN_E_CTR'
        );
    }
}
