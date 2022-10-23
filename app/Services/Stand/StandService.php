<?php

namespace App\Services\Stand;

use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\DependencyService;
use App\Services\NetworkAircraftService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StandService
{
    public const STAND_DEPENDENCY_KEY = 'DEPENDENCY_STANDS';

    private StandAssignmentsService $assignmentsService;

    private AirfieldStandService $airfieldStandService;

    public function __construct(StandAssignmentsService $assignmentsService, AirfieldStandService $airfieldStandService)
    {
        $this->assignmentsService = $assignmentsService;
        $this->airfieldStandService = $airfieldStandService;
    }

    public function getStandsDependency(): Collection
    {
        return $this->airfieldStandService->getAllStandsByAirfield()
            ->mapWithKeys(
                function (Airfield $airfield) {
                    return [
                        $airfield->code => $airfield->stands
                            ->reject(fn (Stand $stand) => $stand->closed_at !== null)
                            ->values()
                            ->map(fn (Stand $stand) => [
                                'id' => $stand->id,
                                'identifier' => $stand->identifier,
                            ]),
                    ];
                }
            )->toBase();
    }

    public function getStandAssignments(): Collection
    {
        return StandAssignment::all()->map(
            function (StandAssignment $assignment) {
                return [
                    'callsign' => $assignment->callsign,
                    'stand_id' => $assignment->stand_id,
                ];
            }
        )->toBase();
    }

    public function getAssignedStandForAircraft(string $aircraft): ?Stand
    {
        $assignment = StandAssignment::with('stand', 'stand.airfield')->find($aircraft);
        return $assignment ? $assignment->stand : null;
    }

    /**
     * Assignments are preferred to reservations as reservations may be overridden by controllers.
     *
     * Get all assigned stands as well as any active reservations.
     */
    public function getAirfieldStandStatus(string $airfield): array
    {
        $stands = Stand::with(
            'wakeCategory',
            'maxAircraft',
            'assignment',
            'occupier',
            'activeReservations',
            'pairedStands.assignment',
            'pairedStands.occupier',
            'pairedStands.activeReservations'
        )
            ->airfield($airfield)
            ->get();

        $stands->sortBy('identifier', SORT_NATURAL);

        $standStatuses = [];

        foreach ($stands as $stand) {
            $standStatuses[] = $this->getStandStatus($stand);
        }

        return $standStatuses;
    }

    private function getStandStatus(Stand $stand): array
    {
        $standData = [
            'identifier' => $stand->identifier,
            'type' => $stand->type ? $stand->type->key : null,
            'airlines' => $stand->airlines->groupBy('icao_code')->map(function (Collection $airlineDestination) {
                return $airlineDestination->filter(function (Airline $airline) {
                    return $airline->pivot->destination;
                })->map(function (Airline $airline) {
                    return $airline->pivot->destination;
                });
            })->toArray(),
            'max_wake_category' => $stand->wakeCategory ? $stand->wakeCategory->code: null,
            'max_aircraft_type' => $stand->maxAircraft ? $stand->maxAircraft->code : null,
        ];

        if ($stand->isClosed()) {
            $standData['status'] = 'closed';
        } elseif ($stand->occupier->first()) {
            $standData['status'] = 'occupied';
            $standData['callsign'] = $stand->occupier->first()->callsign;
        } elseif ($stand->assignment) {
            $standData['status'] = 'assigned';
            $standData['callsign'] = $stand->assignment->callsign;
        } elseif (!$stand->activeReservations->isEmpty()) {
            $standData['status'] = 'reserved';
            $standData['callsign'] = $stand->activeReservations->first()->callsign;
        } elseif (!$stand->reservationsInNextHour->isEmpty()) {
            $standData['status'] = 'reserved_soon';
            $standData['reserved_at'] = $stand->reservationsInNextHour->first()->start;
            $standData['callsign'] = $stand->reservationsInNextHour->first()->callsign;
        } elseif (
            !$stand->pairedStands->filter(function (Stand $stand) {
                return $stand->assignment ||
                    !$stand->occupier->isEmpty() ||
                    !$stand->activeReservations->isEmpty();
            })->isEmpty()
        ) {
            $standData['status'] = 'unavailable';
        } else {
            $standData['status'] = 'available';
        }

        return $standData;
    }

    /**
     * Creates a stand assignment by assigning a particular stand to an aircraft.
     * If the stand is already assigned, that assignment is first removed.
     *
     * @param string $callsign
     * @param int $standId
     */
    public function assignStandToAircraft(string $callsign, int $standId): void
    {
        if (!$this->standExists($standId)) {
            throw new StandNotFoundException(sprintf('Stand with id %d not found', $standId));
        }

        NetworkAircraftService::createPlaceholderAircraft($callsign);
        $currentAssignment = StandAssignment::with('aircraft', 'stand.pairedStands.assignment')
            ->where('stand_id', $standId)
            ->first();

        // Remove the current assignment
        if ($currentAssignment && $currentAssignment->callsign !== $callsign) {
            $this->deleteStandAssignmentByCallsign($currentAssignment->aircraft->callsign);
        }

        // Remove assignments on paired stands
        $stand = Stand::with('pairedStands.assignment')->find($standId);
        foreach ($stand->pairedStands as $pairedStand) {
            if ($pairedStand->assignment && $pairedStand->assignment->callsign !== $callsign) {
                $this->deleteStandAssignmentByCallsign($pairedStand->assignment->callsign);
            }
        }

        $this->assignmentsService->createStandAssignment($callsign, $standId);
    }

    public function deleteStandAssignmentByCallsign(string $callsign): void
    {
        $assignment = StandAssignment::find($callsign);
        if (!$assignment) {
            return;
        }

        $this->assignmentsService->deleteStandAssignment($assignment);
    }

    public function getDepartureStandAssignmentForAircraft(NetworkAircraft $aircraft): ?StandAssignment
    {
        return StandAssignment::where('callsign', $aircraft->callsign)
            ->whereHas(
                'stand.airfield',
                function (Builder $query) use ($aircraft) {
                    $query->where('code', $aircraft->planned_depairport);
                }
            )
            ->first();
    }

    private function standExists(int $standId): bool
    {
        return Stand::where('id', $standId)->exists();
    }

    /**
     * Delete a given stand
     *
     * @param string $airfield
     * @param string $identifier
     * @throws Exception
     */
    public function deleteStand(string $airfield, string $identifier): void
    {
        if (($stand = $this->getStandByAirfieldAndIdentifer($airfield, $identifier)) === null) {
            return;
        }

        $stand->delete();
        DependencyService::touchDependencyByKey(self::STAND_DEPENDENCY_KEY);
    }

    /**
     * Change the identifier for a stand
     *
     * @param string $airfield
     * @param string $oldIdentifier
     * @param string $newIdentifier
     */
    public function changeStandIdentifier(string $airfield, string $oldIdentifier, string $newIdentifier): void
    {
        if (($stand = $this->getStandByAirfieldAndIdentifer($airfield, $oldIdentifier)) === null) {
            return;
        }

        $stand->identifier = $newIdentifier;
        $stand->save();
        DependencyService::touchDependencyByKey(self::STAND_DEPENDENCY_KEY);
    }

    /**
     * Returns a stand by airfield ICAO and identifier
     *
     * @param string $airfield
     * @param string $identifier
     * @return Stand|null
     */
    private function getStandByAirfieldAndIdentifer(string $airfield, string $identifier): ?Stand
    {
        return Stand::with('airfield')->whereHas(
            'airfield',
            function (Builder $query) use ($airfield) {
                $query->where('code', $airfield);
            }
        )->where('identifier', $identifier)
            ->first();
    }

    public function getAircraftEligibleForArrivalStandAllocation(): Collection
    {
        return NetworkAircraft::whereIn(
            'planned_destairport',
            Airfield::all()->pluck('code')->toArray()
        )
            ->notTimedOut()
            ->whereDoesntHave('assignedStand')
            ->get();
    }
}
