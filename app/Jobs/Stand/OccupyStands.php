<?php

namespace App\Jobs\Stand;

use App\Services\Stand\StandOccupationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class OccupyStands implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(StandOccupationService $standService): void
    {
        $startTime = microtime(true);
        Log::debug('OccupyStands: Starting job execution', ['timestamp' => Carbon::now()]);

        $standService->setOccupiedStands();

        $duration = microtime(true) - $startTime;
        Log::debug('OccupyStands: Completed job execution', [
            'duration_seconds' => $duration,
            'timestamp' => Carbon::now()
        ]);
    }
}
