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

        $this->info('Allocating arrival stands');
        NetworkAircraft::all()->each(function (NetworkAircraft $aircraft) {
            $this->standService->allocateStandForAircraft($aircraft);
        });
        $this->info('Finished allocation of arrival stands');
    }
}
