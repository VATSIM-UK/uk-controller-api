<?php

namespace App\Jobs\Stand;

use App\Services\Stand\ArrivalAllocationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class RemoveDisconnectedArrivalStands implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(ArrivalAllocationService $arrivalAllocationService): void
    {
        $startTime = microtime(true);
        Log::debug('RemoveDisconnectedArrivalStands: Starting job execution', ['timestamp' => Carbon::now()]);

        $arrivalAllocationService->removeArrivalStandsFromDisconnectedAircraft();

        $duration = microtime(true) - $startTime;
        Log::debug('RemoveDisconnectedArrivalStands: Completed job execution', [
            'duration_seconds' => $duration,
            'timestamp' => Carbon::now()
        ]);
    }
}
