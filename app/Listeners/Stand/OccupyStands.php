<?php

namespace App\Listeners\Stand;

use App\Events\NetworkAircraftUpdatedEvent;
use App\Services\StandService;

class OccupyStands
{
    /**
     * @var StandService
     */
    private $standService;

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
