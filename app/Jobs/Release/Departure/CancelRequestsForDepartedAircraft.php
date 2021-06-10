<?php

namespace App\Jobs\Release\Departure;

use App\Services\DepartureReleaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CancelRequestsForDepartedAircraft implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(DepartureReleaseService $releaseService): void
    {
        $releaseService->cancelReleasesForAirborneAircraft();
    }
}
