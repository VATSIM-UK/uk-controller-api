<?php

namespace App\Listeners\Stand;

use App\Events\StandVacatedEvent;
use App\Services\StandService;

class UnassignVacatedDepartureStand
{
    private StandService $standService;

    public function __construct(StandService $standService)
    {
        $this->standService = $standService;
    }

    public function handle(StandVacatedEvent $event) : bool
    {
        $aircraft = $event->getAircraft();
        if (
            ($assignedStand = $this->standService->getAssignedStandForAircraft($aircraft->callsign)) !== null &&
            $assignedStand->airfield->code === $aircraft->planned_depairport
        ) {
            $this->standService->deleteStandAssignmentByCallsign($aircraft->callsign);
        }

        return true;
    }
}
