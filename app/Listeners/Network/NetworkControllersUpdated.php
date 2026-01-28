<?php

namespace App\Listeners\Network;

use App\Jobs\Network\IdentifyActiveControllerPositions;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class NetworkControllersUpdated implements ShouldQueue, ShouldBeUnique
{
    // This listener should be unique for 30 seconds
    public $uniqueFor = 30;

    public function handle()
    {
        $startTime = microtime(true);
        Log::debug('NetworkControllersUpdated: Starting event listener', ['timestamp' => Carbon::now()]);

        Bus::chain(
            [
                new IdentifyActiveControllerPositions()
            ]
        )->dispatch();

        $duration = microtime(true) - $startTime;
        Log::debug('NetworkControllersUpdated: Dispatched job chain', [
            'duration_seconds' => $duration,
            'timestamp' => Carbon::now()
        ]);

        return true;
    }
}
