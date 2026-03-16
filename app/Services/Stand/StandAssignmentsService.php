<?php

namespace App\Services\Stand;

use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\NetworkAircraftService;
use Illuminate\Support\Facades\DB;

class StandAssignmentsService
{
    private readonly RecordsAssignmentHistory $historyService;

    public function __construct(RecordsAssignmentHistory $historyService)
    {
        $this->historyService = $historyService;
    }

    public function assignmentForCallsign(string $callsign): ?StandAssignment
    {
        return StandAssignment::with('assignmentHistory')->find($callsign);
    }

    public function deleteAssignmentIfExists(NetworkAircraft $aircraft): void
    {
        if ($assignment = $this->assignmentForCallsign($aircraft->callsign)) {
            $this->deleteStandAssignment($assignment);
        }
    }

    public function deleteStandAssignment(StandAssignment $assignment): void
    {
        $assignmentSource = $assignment->assignment_source;
        $this->deleteAssignmentAndHistoryData($assignment);
        $this->unassignedEvent($assignment->callsign, $assignmentSource);
    }

    /**
     * Create a stand assignment, removing any existing assignments for the stand and moving the aircraft to the
     * "new" stand if already assigned.
     */
    public function createStandAssignment(string $callsign, int $standId, string $assignmentType): void
    {
        if (!($stand = Stand::with('pairedStands')->find($standId))) {
            throw new StandNotFoundException(sprintf('Stand with id %d not found', $standId));
        }

        [$assignment, $existingAssignments] = DB::transaction(function () use ($callsign, $stand, $assignmentType) {
            // Remove assignments for this and paired stands
            $existingAssignments = StandAssignment::with('stand')
                ->where('stand_id', $stand->id)
                ->where('callsign', '<>', $callsign)
                ->union(
                    StandAssignment::whereIn(
                        'stand_id',
                        $stand->pairedStands->pluck('id')
                    )
                )
                ->get();

            $existingAssignments->each(function (StandAssignment $assignment) {
                $this->deleteAssignmentAndHistoryData($assignment);
            });

            // Create new stand assignment
            NetworkAircraftService::createPlaceholderAircraft($callsign);
            $assignment = StandAssignment::updateOrCreate(
                ['callsign' => $callsign],
                [
                    'stand_id' => $stand->id,
                    'assignment_source' => $this->assignmentSourceFromType($assignmentType),
                ]
            );

            $assignmentContext = new StandAssignmentContext(
                $assignment,
                $assignmentType,
                $existingAssignments,
                $assignment->aircraft
            );
            $this->historyService->createHistoryItem($assignmentContext);

            return [$assignment, $existingAssignments];
        });

        $existingAssignments->each(function (StandAssignment $assignment) {
            $this->unassignedEvent($assignment->callsign, $assignment->assignment_source);
        });
        event(new StandAssignedEvent($assignment));
    }

    private function unassignedEvent(string $callsign, ?string $assignmentSource): void
    {
        event(new StandUnassignedEvent($callsign, $assignmentSource));
    }

    private function assignmentSourceFromType(string $assignmentType): string
    {
        if ($assignmentType === 'User') {
            return StandAssignment::SOURCE_MANUAL;
        }

        if (str_ends_with($assignmentType, 'UserRequestedArrivalStandAllocator')) {
            return StandAssignment::SOURCE_RESERVATION_ALLOCATOR;
        }

        if (
            str_ends_with($assignmentType, 'CidReservedArrivalStandAllocator')
            || str_ends_with($assignmentType, 'CallsignFlightplanReservedArrivalStandAllocator')
        ) {
            return StandAssignment::SOURCE_VAA_ALLOCATOR;
        }

        return StandAssignment::SOURCE_SYSTEM_AUTO;
    }

    private function deleteAssignmentAndHistoryData(StandAssignment $assignment): void
    {
        DB::transaction(function () use ($assignment) {
            $assignment->delete();
            $this->historyService->deleteHistoryFor($assignment);
        });
    }
}
