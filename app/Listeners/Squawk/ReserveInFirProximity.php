<?php

namespace App\Listeners\Squawk;

use App\Events\NetworkAircraftUpdatedEvent;
use App\Services\LocationService;
use App\Services\SquawkService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Location\Coordinate;
use Location\Distance\Haversine;

class ReserveInFirProximity
{
    const MIN_DISTANCE = 650.0;
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
                break;
            }

            // If the aircraft is close enough, lets try to reserve the squawk
            $newAssignment = $this->squawkService->reserveSquawkForAircraft($aircraft->callsign);
            if ($currentAssignment && $newAssignment) {
                Log::info(
                    sprintf(
                        'Reclaiming squawk code %s from %s due to code change, new code %s was reserved',
                        $currentAssignment->getCode(),
                        $currentAssignment->getCallsign(),
                        $newAssignment->getCode())
                );
            } else if ($currentAssignment && !$newAssignment) {
                Log::info(
                    sprintf(
                        'Reclaiming squawk code %s from %s due to code change, no new code was reserved, transponder is %s',
                        $currentAssignment->getCode(),
                        $currentAssignment->getCallsign(),
                        $aircraft->transponder
                    )
                );
            } else if ($newAssignment) {
                Log::info(
                    sprintf(
                        'Reserving squawk code %s for %s due to FIR proximity',
                        $currentAssignment->getCode(),
                        $currentAssignment->getCallsign()
                    )
                );
            }

            break;
        }

        return true;
    }
}
