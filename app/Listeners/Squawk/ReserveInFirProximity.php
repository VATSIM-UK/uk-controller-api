<?php

namespace App\Listeners\Squawk;

use App\Events\NetworkAircraftUpdatedEvent;
use App\Services\LocationService;
use App\Services\SquawkService;
use Carbon\Carbon;
use Location\Coordinate;
use Location\Distance\Haversine;

class ReserveInFirProximity
{
    const MIN_DISTANCE = 600.0;
    const CONSISTENT_SQUAWK_MINUTES = 2;

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
        $aircraft = $event->getAircraft();
        foreach ($this->measuringPoints as $coordinate) {
            // If the aircraft is too far away, skip
            if (
                LocationService::metersToNauticalMiles(
                    $coordinate->getDistance($aircraft->latLong, new Haversine())
                ) > self::MIN_DISTANCE
            ) {
                continue;
            }

            // The aircraft is squawking its assigned code or has only recently changed code, so let it go
            if (
                (($currentAssignment = $this->squawkService->getAssignedSquawk(
                        $aircraft->callsign
                    )) && $aircraft->transponder == $currentAssignment->getCode()) ||
                $aircraft->transponder_last_updated_at > Carbon::now()->subMinutes(self::CONSISTENT_SQUAWK_MINUTES)
            ) {
                return true;
            }

            // If the aircraft is close enough, lets try to reserve the squawk
            $this->squawkService->reserveSquawkForAircraft($aircraft->callsign);
            return true;
        }

        return true;
    }
}
