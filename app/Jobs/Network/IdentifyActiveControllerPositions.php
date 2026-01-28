<?php

namespace App\Jobs\Network;

use App\Services\NetworkControllerService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class IdentifyActiveControllerPositions implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(NetworkControllerService $controllerService): void
    {
        $startTime = microtime(true);
        Log::debug('IdentifyActiveControllerPositions: Starting job execution', ['timestamp' => Carbon::now()]);

        $controllerService->updatedMatchedControllerPositions();

        $duration = microtime(true) - $startTime;
        Log::debug('IdentifyActiveControllerPositions: Completed job execution', [
            'duration_seconds' => $duration,
            'timestamp' => Carbon::now()
        ]);
    }
}
