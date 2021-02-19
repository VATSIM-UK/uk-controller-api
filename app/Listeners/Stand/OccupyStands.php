<?php

namespace App\Listeners\Stand;

use App\Events\NetworkAircraftUpdatedEvent;
use App\Services\StandService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * This job involves a lot of proximity calculations for stands,
 * so queue this one.
 */
class OccupyStands implements ShouldQueue
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
