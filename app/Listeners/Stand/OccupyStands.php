<?php

namespace App\Listeners\Stand;

use App\Events\NetworkAircraftUpdatedEvent;
use App\Services\StandService;
use Illuminate\Contracts\Queue\ShouldQueue;

class OccupyStands implements ShouldQueue
{
    private StandService $standService;

    public function __construct(StandService $standService)
    {
        $this->standService = $standService;
    }

    public function handle(NetworkAircraftUpdatedEvent $event) : bool
    {
        $this->standService->setOccupiedStand($event->getAircraft());
        return true;
    }
}
