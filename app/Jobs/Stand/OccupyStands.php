<?php

namespace App\Jobs\Stand;

use App\Services\Stand\StandOccupationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OccupyStands implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(StandOccupationService $standService): void
    {
        $standService->setOccupiedStands();
    }
}
