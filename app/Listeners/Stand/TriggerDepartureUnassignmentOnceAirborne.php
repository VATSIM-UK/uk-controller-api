<?php

namespace App\Listeners\Stand;

use App\Events\NetworkAircraftUpdatedEvent;
use App\Services\StandService;

class TriggerDepartureUnassignmentOnceAirborne
{
    const MIN_UNASSIGNMENT_GROUNDSPEED = 50;
    const MIN_UNASSIGNMENT_ALTITUDE = 1000;

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
        $aircraft = $event->getAircraft();
        if (
            $aircraft->groundspeed < self::MIN_UNASSIGNMENT_GROUNDSPEED ||
            $aircraft->altitude < self::MIN_UNASSIGNMENT_ALTITUDE ||
            !$this->standService->getDepartureStandAssignmentForAircraft($aircraft)
        ) {
            return true;
        }

        $this->standService->deleteStandAssignmentByCallsign($aircraft->callsign);
        return true;
    }
}
