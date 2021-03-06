<?php

namespace App\Listeners\Stand;

use App\Events\StandOccupiedEvent;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\StandService;

class AssignOccupiedStandsForDeparture
{
    private StandService $standService;

    public function __construct(StandService $standService)
    {
        $this->standService = $standService;
    }

    public function handle(StandOccupiedEvent $event) : bool
    {
        $aircraft = $event->getAircraft();
        $stand = $event->getStand();
        if (
            ($aircraft->planned_depairport === null || $aircraft->planned_depairport === $stand->airfield->code) &&
            !$this->aircraftAlreadyAssignedStand($aircraft)
        ) {
            $this->standService->assignStandToAircraft($aircraft->callsign, $stand->id);
        }

        return true;
    }

    private function aircraftAlreadyAssignedStand(NetworkAircraft $aircraft): bool
    {
        return StandAssignment::where('callsign', $aircraft->callsign)
            ->exists();
    }
}
