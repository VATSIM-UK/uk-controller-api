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

    private function standExists(int $standId): bool
    {
        return Stand::where('id', $standId)->exists();
    }
}
