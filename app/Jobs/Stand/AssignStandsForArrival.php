<?php

namespace App\Jobs\Stand;

use App\Services\Stand\ArrivalAllocationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class AssignStandsForArrival implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(ArrivalAllocationService $arrivalAllocationService): void
    {
        $startTime = microtime(true);
        Log::debug('AssignStandsForArrival: Starting job execution', ['timestamp' => Carbon::now()]);

        $arrivalAllocationService->allocateStandsAtArrivalAirfields();

        $duration = microtime(true) - $startTime;
        Log::debug('AssignStandsForArrival: Completed job execution', [
            'duration_seconds' => $duration,
            'timestamp' => Carbon::now()
        ]);
    }
}
