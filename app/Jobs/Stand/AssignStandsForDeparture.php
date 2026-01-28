<?php

namespace App\Jobs\Stand;

use App\Services\Stand\DepartureAllocationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class AssignStandsForDeparture implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(DepartureAllocationService $standService): void
    {
        $startTime = microtime(true);
        Log::debug('AssignStandsForDeparture: Starting job execution', ['timestamp' => Carbon::now()]);

        $standService->assignStandsForDeparture();

        $duration = microtime(true) - $startTime;
        Log::debug('AssignStandsForDeparture: Completed job execution', [
            'duration_seconds' => $duration,
            'timestamp' => Carbon::now()
        ]);
    }
}
