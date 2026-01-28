<?php

namespace App\Jobs\Squawk;

use App\Services\SquawkService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ReserveActiveSquawks implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(SquawkService $squawkService): void
    {
        $startTime = microtime(true);
        Log::debug('ReserveActiveSquawks: Starting job execution', ['timestamp' => Carbon::now()]);

        $squawkService->reserveActiveSquawks();

        $duration = microtime(true) - $startTime;
        Log::debug('ReserveActiveSquawks: Completed job execution', [
            'duration_seconds' => $duration,
            'timestamp' => Carbon::now()
        ]);
    }
}
