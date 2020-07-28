<?php

namespace App\Listeners\Squawk;

use App\Events\NetworkAircraftUpdatedEvent;
use App\Services\LocationService;
use App\Services\SquawkService;
use Illuminate\Support\Facades\Log;
use Location\Coordinate;
use Location\Distance\Haversine;

class ReclaimIfLeftFirProximity
{
    const MIN_DISTANCE = 650.0;

    /**
     * @var Coordinate[]
     */
    private $measuringPoints;

    /**
     * @var SquawkService
     */
    private $squawkService;

    /**
     * ReserveSquawkIfInFirProximity constructor.
     * @param SquawkService $squawkService
     * @param array $measuringPoints
     */
    public function __construct(SquawkService $squawkService, array $measuringPoints)
    {
        $this->squawkService = $squawkService;
        $this->measuringPoints = $measuringPoints;
    }

    /**
     * Handle any squawk allocation event
     *
     * @param NetworkAircraftUpdatedEvent $event
     * @return bool
     */
    public function handle(NetworkAircraftUpdatedEvent $event): bool
    {
        if (!($assignment = $this->squawkService->getAssignedSquawk($event->getAircraft()->callsign))) {
            return true;
        }

        $aircraft = $event->getAircraft();
        foreach ($this->measuringPoints as $coordinate) {
            if (
                LocationService::metersToNauticalMiles(
                    $coordinate->getDistance($aircraft->latLong, new Haversine())
                ) < self::MIN_DISTANCE
            ) {
                continue;
            }

            Log::info(
                sprintf(
                    'Reclaiming squawk code %s from %s due to leaving FIR proximity',
                    $assignment->getCode(),
                    $assignment->getCallsign()
                )
            );
            $this->squawkService->deleteSquawkAssignment($aircraft->callsign);
            break;
        }

        return true;
    }
}
