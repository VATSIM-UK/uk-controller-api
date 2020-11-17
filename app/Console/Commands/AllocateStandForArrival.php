<?php

namespace App\Console\Commands;

use App\Models\Airfield\Airfield;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\LocationService;
use App\Services\StandService;
use Illuminate\Console\Command;
use Location\Distance\Haversine;

class AllocateStandForArrival extends Command
{
    protected $signature = 'stands:assign-arrival';

    protected $description = 'Assigns arrival stands to all aircraft that require it';

    /**
     * How many minutes before arrival the stand should be assigned
     */
    private const ASSIGN_STAND_MINUTES_BEFORE = 15.0;

    /**
     * @var StandService
     */
    private $standService;

    public function __construct(StandService $standService)
    {
        parent::__construct();
        $this->standService = $standService;
    }

    public function handle()
    {
        $this->info('Allocating arrival stands');
        NetworkAircraft::all()->each(function (NetworkAircraft $aircraft) {
            $this->standService->allocateStandForAircraft($aircraft);
        });
        $this->info('Finished allocation of arrival stands');
    }
}
