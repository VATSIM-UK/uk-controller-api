<?php

namespace App\Services\Stand;

use App\Allocator\Stand\ArrivalStandAllocatorInterface;
use App\Events\StandAssignedEvent;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\LocationService;
use Location\Distance\Haversine;

class ArrivalAllocationService
{
    /**
     * How many minutes before arrival the stand should be assigned
     */
    private const ASSIGN_STAND_MINUTES_BEFORE = 15.0;

    private readonly StandAssignmentsService $assignmentsService;

    /**
     * @var ArrivalStandAllocatorInterface[]
     */
    private readonly array $allocators;

    public function __construct(StandAssignmentsService $assignmentsService, array $allocators)
    {
        $this->assignmentsService = $assignmentsService;
        $this->allocators = $allocators;
    }

    public function allocateStandsAtArrivalAirfields(): void
    {
        $this->deleteAssignmentsForAircraftWithChangedDestination();
        $this->allocateStandsForArrivingAircraft();
    }

    private function deleteAssignmentsForAircraftWithChangedDestination(): void
    {
        StandAssignment::join('network_aircraft', 'network_aircraft.callsign', '=', 'stand_assignments.callsign')
            ->join('stands', 'stands.id', '=', 'stand_assignments.stand_id')
            ->join('airfield', 'airfield.id', '=', 'stands.airfield_id')
            ->whereRaw('airfield.code <> network_aircraft.planned_destairport')
            ->whereRaw('airfield.code <> network_aircraft.planned_depairport')
            ->select('stand_assignments.*')
            ->get()
            ->each(function (StandAssignment $standAssignment) {
                $this->assignmentsService->deleteStandAssignment($standAssignment);
            });
    }

    /**
     * Use the stand assignment rules to allocate a stand for a given aircraft
     */
    private function allocateStandsForArrivingAircraft(): void
    {
        NetworkAircraft::whereIn(
            'planned_destairport',
            Airfield::all()->pluck('code')->toArray()
        )
            ->notTimedOut()
            ->whereDoesntHave('assignedStand')
            ->get()
            ->filter(fn (NetworkAircraft $aircraft) => $this->shouldAllocateStand($aircraft))
            ->each(function (NetworkAircraft $aircraft) {
                foreach ($this->allocators as $allocator) {
                    if ($allocation = $allocator->allocate($aircraft)) {
                        event(new StandAssignedEvent($allocation));
                        return;
                    }
                }
            });
    }

    /**
     * Criteria for whether a stand should be allocated
     *
     * 1. Cannot have the same departure and arrival airport (to cater for circuits)
     * 2. Aircraft must not have an existing stand assignment
     * 3. The arrival airfield must exist
     * 4. The aircraft has to be moving (to prevent divide by zero errors)
     * 5. The aircraft must have a discernible aircraft type
     * 6. The aircraft type should be one that we allocate stands to
     * 7. The aircraft needs to be within a certain number of minutes from landing
     */
    private function shouldAllocateStand(NetworkAircraft $aircraft): bool
    {
        return $aircraft->planned_depairport !== $aircraft->planned_destairport &&
            StandAssignment::where('callsign', $aircraft->callsign)->doesntExist() &&
            ($arrivalAirfield = Airfield::where('code', $aircraft->planned_destairport)->first()) !== null &&
            $aircraft->groundspeed &&
            ($aircraftType = Aircraft::where('code', $aircraft->aircraftType)->first()) &&
            $aircraftType->allocate_stands &&
            $this->getTimeFromAirfieldInMinutes($aircraft, $arrivalAirfield) < self::ASSIGN_STAND_MINUTES_BEFORE;
    }

    /**
     * Ground speed is kts (nautical miles per hour), so for minutes multiply that by 60.
     *
     * @param NetworkAircraft $aircraft
     * @param Airfield $airfield
     * @return float
     */
    private function getTimeFromAirfieldInMinutes(NetworkAircraft $aircraft, Airfield $airfield): float
    {
        $distanceToAirfieldInNm = LocationService::metersToNauticalMiles(
            $aircraft->latLong->getDistance($airfield->coordinate, new Haversine())
        );
        $groundspeed = $aircraft->groundspeed === 0 ? 1 : $aircraft->groundspeed;

        return (float) ($distanceToAirfieldInNm / $groundspeed) * 60.0;
    }

    public function getAllocators(): array
    {
        return $this->allocators;
    }
}
