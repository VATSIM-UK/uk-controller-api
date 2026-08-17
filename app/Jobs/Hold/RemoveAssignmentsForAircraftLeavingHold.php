<?php

namespace App\Jobs\Hold;

use App\Services\HoldService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RemoveAssignmentsForAircraftLeavingHold implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function handle(HoldService $holdService): void
    {
        $holdService->removeStaleAssignments();
    }
}
