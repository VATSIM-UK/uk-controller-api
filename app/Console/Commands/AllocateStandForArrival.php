<?php

namespace App\Console\Commands;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\StandService;
use Illuminate\Console\Command;

class AllocateStandForArrival extends Command
{
    protected $signature = 'stands:assign-arrival';
    protected $description = 'Assigns arrival stands to all aircraft that require it';

    private StandService $standService;

    public function __construct(StandService $standService)
    {
        parent::__construct();
        $this->standService = $standService;
    }

    public function handle()
    {
        if (!config('stands.auto_allocate', false)) {
            $this->info('Skipping arrival stand allocation');
            return;
        }
        $allAircraft = NetworkAircraft::all();
        $this->info('Checking for diversions to deallocate');
        $allAircraft->each(function (NetworkAircraft $aircraft) {
            $this->standService->removeAllocationIfDestinationChanged($aircraft);
        });
        $this->info('Allocating arrival stands');
        $allAircraft->each(function (NetworkAircraft $aircraft) {
            $this->standService->allocateStandForAircraft($aircraft);
        });
        $this->info('Finished allocation of arrival stands');
    }
}
