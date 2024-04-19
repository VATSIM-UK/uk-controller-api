<?php

namespace App\Jobs\Stand;

use App\Services\Stand\ArrivalAllocationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RemoveDisconnectedArrivalStands implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(ArrivalAllocationService $arrivalAllocationService): void
    {
        $arrivalAllocationService->removeArrivalStandsFromDisconnectedAircraft();
    }
}
