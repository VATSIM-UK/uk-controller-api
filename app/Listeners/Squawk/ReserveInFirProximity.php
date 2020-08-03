<?php

namespace App\Listeners\Squawk;

use App\Events\NetworkAircraftUpdatedEvent;
use App\Models\Squawk\Reserved\NonAssignableSquawkCode;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\LocationService;
use App\Services\SquawkService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Location\Coordinate;
use Location\Distance\Haversine;

class ReserveInFirProximity
{
    const MIN_DISTANCE = 650.0;
    const MAX_GROUND_RESERVATION_SPEED = 45;
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

            // We're close enough to try the reservation, check if we should reserve the squawk
            if ($this->shouldNotReserveAssignment($aircraft)) {
                break;
            }

            // Try to reserve the squawk
            $currentAssignment = $this->squawkService->getAssignedSquawk($aircraft->callsign);
            $newAssignment = $this->squawkService->reserveSquawkForAircraft($aircraft->callsign);
            if ($currentAssignment && $newAssignment) {
                Log::info(
                    sprintf(
                        'Reclaiming squawk code %s from %s due to code change, new code %s was reserved',
                        $currentAssignment->getCode(),
                        $currentAssignment->getCallsign(),
                        $newAssignment->getCode()
                    )
                );
            } elseif ($currentAssignment && !$newAssignment) {
                Log::info(
                    sprintf(
                        'Reclaiming squawk code %s from %s due to code change, no new code was reserved, transponder is %s',
                        $currentAssignment->getCode(),
                        $currentAssignment->getCallsign(),
                        $aircraft->transponder
                    )
                );
            } elseif ($newAssignment) {
                Log::info(
                    sprintf(
                        'Reserving squawk code %s for %s due to FIR proximity',
                        $newAssignment->getCode(),
                        $newAssignment->getCallsign()
                    )
                );
            }

            break;
        }

        return true;
    }

    private function shouldNotReserveAssignment(NetworkAircraft $aircraft): bool
    {
        return $this->isSquawkingAssignedCode($aircraft) ||
            $this->transponderRecentlyUpdated($aircraft) ||
            $this->aircraftIsSquawkingNonAssignableCodeOnTheGround($aircraft) ||
            $aircraft->squawkingMayday() ||
            $aircraft->squawkingBannedSquawk() ||
            $aircraft->squawkingRadioFailure();
    }

    private function isSquawkingAssignedCode(NetworkAircraft $aircraft): bool
    {
        return ($assignment = $this->squawkService->getAssignedSquawk($aircraft->callsign))
            && $assignment->getCode() === $aircraft->squawk;
    }

    private function transponderRecentlyUpdated(NetworkAircraft $aircraft): bool
    {
        return $aircraft->transponder_last_updated_at > Carbon::now()->subMinutes(self::CONSISTENT_SQUAWK_MINUTES);
    }

    private function aircraftIsSquawkingNonAssignableCodeOnTheGround(NetworkAircraft $aircraft): bool
    {
        return $aircraft->groundspeed < self::MAX_GROUND_RESERVATION_SPEED &&
            NonAssignableSquawkCode::where('code', $aircraft->squawk)->exists();
    }
}
