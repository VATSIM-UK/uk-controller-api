<?php

namespace App\Jobs\Hold;

use App\Services\HoldService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class RemoveAssignmentsForAircraftLeavingHold implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(HoldService $holdService): void
    {
        $startTime = microtime(true);
        Log::debug('RemoveAssignmentsForAircraftLeavingHold: Starting job execution', ['timestamp' => Carbon::now()]);

        $holdService->removeStaleAssignments();

        $duration = microtime(true) - $startTime;
        Log::debug('RemoveAssignmentsForAircraftLeavingHold: Completed job execution', [
            'duration_seconds' => $duration,
            'timestamp' => Carbon::now()
        ]);
    }
}
