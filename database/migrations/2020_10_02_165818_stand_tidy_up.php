<?php

use App\Services\Stand\StandService;
use Illuminate\Database\Migrations\Migration;

class StandTidyUp extends Migration
{
    /**
     * @var StandService
     */
    private $standService;

    public function __construct()
    {
        $this->standService = app(StandService::class);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Delete Luton ATC tower and fire station
        $this->standService->deleteStand('EGGW', 'ATC');
        $this->standService->deleteStand('EGGW', 'FS');

        // Delete Edinburgh GA apron
        $this->standService->deleteStand('EGPH', 'GA');

        // Update Gatwick stands missing a number
        $this->standService->changeStandIdentifier('EGKK', 'L', '13L');
        $this->standService->changeStandIdentifier('EGKK', 'R', '13R');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No going back
    }
}
