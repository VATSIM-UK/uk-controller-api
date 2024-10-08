<?php

namespace App\Services\Stand;

use App\Allocator\Stand\ArrivalStandAllocator;
use App\Allocator\Stand\RankableArrivalStandAllocator;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\LocationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Location\Distance\Haversine;

class ArrivalAllocationService
{
    /**
     * How many minutes before arrival the stand should be assigned
     */
    private const ASSIGN_STAND_MINUTES_BEFORE = 15.0;
    
    /**
     * How recently an aircraft should have been seen to be considered for stand assignment.
     */
    private const ASSIGN_STAND_IF_SEEN_WITHIN_MINUTES = 1;

    /**
     * How many minutes before we remove the stand assignment for an aircraft that has disconnected.
     */
    private const DISCONNECTED_AIRCRAFT_TIMEOUT_MINUTES = 5;

    /**
     * How many minutes before we remove the stand assignment for an aircraft
     * that has disconnected but is occupying the stand.
     */
    private const DISCONNECTED_AIRCRAFT_OCCUPIED_TIMEOUT_MINUTES = 2;

    private readonly StandAssignmentsService $assignmentsService;

    /**
     * @var ArrivalStandAllocator[]
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
     * Allocate a stand for a given aircraft using the stand assignment rules.
     */
    public function autoAllocateArrivalStandForAircraft(NetworkAircraft $aircraft): ?int
    {
        foreach ($this->allocators as $allocator) {
            if ($allocation = $allocator->allocate($aircraft)) {
                $this->assignmentsService->createStandAssignment(
                    $aircraft->callsign,
                    $allocation,
                    get_class($allocator)
                );
                return $allocation;
            }
        }

        return null;
    }

    /**
     * Use the stand assignment rules to allocate a stand for a given aircraft
     */
    private function allocateStandsForArrivingAircraft(): void
    {
        $this->getAircraftThatCanHaveArrivalStandsAllocated()
            ->filter(fn (NetworkAircraft $aircraft) => $this->aircraftWithAssignmentDistance($aircraft))
            ->each(function (NetworkAircraft $aircraft) {
                $this->autoAllocateArrivalStandForAircraft($aircraft);
            });
    }

    /**
     * Criteria for whether a stand can be allocated
     *
     * 1. Cannot have the same departure and arrival airport (to cater for circuits)
     * 2. Aircraft must not have an existing stand assignment
     * 3. The arrival airfield must exist
     * 4. The aircraft has to be moving (to prevent divide by zero errors)
     * 5. The aircraft must have a discernible aircraft type
     * 6. The aircraft type should be one that we allocate stands to
     */
    private function getAircraftThatCanHaveArrivalStandsAllocated(): Collection
    {
        return NetworkAircraft::join('airfield', 'airfield.code', '=', 'network_aircraft.planned_destairport')
            ->join('aircraft', 'network_aircraft.aircraft_id', '=', 'aircraft.id')
            ->leftJoin('stand_assignments', 'stand_assignments.callsign', '=', 'network_aircraft.callsign')
            ->whereRaw('network_aircraft.planned_destairport <> network_aircraft.planned_depairport')
            ->where('aircraft.allocate_stands', '<>', 0)
            ->where('network_aircraft.groundspeed', '>', 0)
            ->whereNull('stand_assignments.callsign')
            ->seenSince(Carbon::now()->subMinutes(self::ASSIGN_STAND_IF_SEEN_WITHIN_MINUTES))
            ->select('network_aircraft.*')
            ->get();
    }

    private function aircraftWithAssignmentDistance(NetworkAircraft $aircraft): bool
    {
        return $this->getTimeFromAirfieldInMinutes(
            $aircraft,
            Airfield::fromCode($aircraft->planned_destairport)
        ) < self::ASSIGN_STAND_MINUTES_BEFORE;
    }

    /**
     * When allocating arrival stands, we only want to do it if the aircraft is close to its arrival airfield.
     *
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

    public function getAllocationRankingForAircraft(NetworkAircraft $aircraft): Collection
    {
        $ranking = collect();

        foreach ($this->allocators as $allocator) {
            if (!$allocator instanceof RankableArrivalStandAllocator) {
                continue;
            }

            $ranking[get_class($allocator)] = $allocator->getRankedStandAllocation($aircraft)
                ->groupBy('rank')
                ->values();
        }

        return $ranking;
    }

    /**
     * Recommends stands for a given aircraft from the first ranking group, up to the number requested.
     */
    public function recommendStand(int $number, NetworkAircraft $aircraft): array
    {
        $rankedStands = $this->getAllocationRankingForAircraft($aircraft);
        $recommendations = collect();

        // Go through each allocator
        foreach ($rankedStands as $standsForAllocator) {
            if (empty($standsForAllocator)) {
                continue;
            }

            // Go through the ranks, select at most the right number of stands to get to our target in random order
            foreach ($standsForAllocator as $stands) {
                if (count($recommendations) === $number) {
                    break;
                }

                $recommendations = $recommendations->merge(
                    $stands->shuffle()
                        ->filter(fn (Stand $stand) => !$recommendations->contains($stand->identifier))
                        ->take($number - count($recommendations))
                        ->pluck('identifier')
                );
            }
        }

        return $recommendations->toArray();
    }

    public function removeArrivalStandsFromDisconnectedAircraft(): void
    {
        // First of all, remove any arrival stands from aircraft that have been disconnected for more than 5 minutes
        StandAssignment::join('network_aircraft', 'network_aircraft.callsign', '=', 'stand_assignments.callsign')
            ->join('stands', 'stands.id', '=', 'stand_assignments.stand_id')
            ->join('airfield', 'airfield.id', '=', 'stands.airfield_id')
            ->whereRaw('airfield.code = network_aircraft.planned_destairport')
            ->where('network_aircraft.updated_at', '<', now()->subMinutes(self::DISCONNECTED_AIRCRAFT_TIMEOUT_MINUTES))
            ->select('stand_assignments.*')
            ->get()
            ->each(function (StandAssignment $standAssignment) {
                $this->assignmentsService->deleteStandAssignment($standAssignment);
            });

        // Now remove any arrival stands from aircraft that have been disconnected for more than 2 minutes
        // But they actually arrived on the stand and are occupying it
        StandAssignment::join('network_aircraft', 'network_aircraft.callsign', '=', 'stand_assignments.callsign')
            ->join('stands', 'stands.id', '=', 'stand_assignments.stand_id')
            ->join('airfield', 'airfield.id', '=', 'stands.airfield_id')
            ->join('aircraft_stand', 'aircraft_stand.callsign', '=', 'stand_assignments.callsign')
            ->whereRaw('airfield.code = network_aircraft.planned_destairport')
            ->where('network_aircraft.updated_at', '<', now()->subMinutes(self::DISCONNECTED_AIRCRAFT_OCCUPIED_TIMEOUT_MINUTES))
            ->whereRaw('aircraft_stand.stand_id = stand_assignments.stand_id')
            ->select('stand_assignments.*')
            ->get()
            ->each(function (StandAssignment $standAssignment) {
                $this->assignmentsService->deleteStandAssignment($standAssignment);
            });
    }
}
