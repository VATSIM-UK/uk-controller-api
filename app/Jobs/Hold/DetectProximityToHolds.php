<?php

namespace App\Jobs\Hold;

use App\Services\HoldService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DetectProximityToHolds implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(HoldService $holdService): void
    {
        $holdService->checkAircraftHoldProximity();
    }
}
