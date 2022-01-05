<?php

namespace App\Jobs\Stand;

use App\Services\Stand\StandService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AssignStandsForDeparture implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(StandService $standService): void
    {
        $standService->assignStandsForDeparture();
    }
}
