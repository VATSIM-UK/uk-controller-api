<?php

namespace App\Jobs\Stand;

use App\Services\Stand\DepartureAllocationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AssignStandsForDeparture implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function handle(DepartureAllocationService $standService): void
    {
        $standService->assignStandsForDeparture();
    }
}
