<?php

namespace App\Services\Stand;

use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StandAssignmentsService
{
    private readonly StandAssignmentsHistoryService $historyService;

    public function __construct(StandAssignmentsHistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    public function assignmentForCallsign(string $callsign): ?StandAssignment
    {
        return StandAssignment::find($callsign);
    }

    public function deleteStandAssignment(StandAssignment $assignment): void
    {
        $this->deleteAssignmentAndHistoryData($assignment);
        $this->unassignedEvent($assignment);
    }

    /**
     * Create a stand assignment, removing any existing assignments for the stand and moving the aircraft to the
     * "new" stand if already assigned.
     */
    public function createStandAssignment(string $callsign, int $standId): void
    {
        if (!($stand = Stand::with('pairedStands')->find($standId))) {
            throw new StandNotFoundException(sprintf('Stand with id %d not found', $standId));
        }

        [$assignment, $existingAssignments] = DB::transaction(function () use ($callsign, $stand) {
            // Remove assignments for this and paired stands
            $existingAssignments = StandAssignment::where('stand_id', $stand->id)
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
            $assignment = StandAssignment::updateOrCreate(
                ['callsign' => $callsign],
                [
                    'stand_id' => $stand->id,
                ]
            );
            $this->historyService->createHistoryItem($assignment);

            return [$assignment, $existingAssignments];
        });

        $existingAssignments->each(function (StandAssignment $assignment) {
            $this->unassignedEvent($assignment);
        });
        event(new StandAssignedEvent($assignment));
    }

    private function unassignedEvent(StandAssignment $assignment): void
    {
        event(new StandUnassignedEvent($assignment->callsign));
    }

    private function deleteAssignmentAndHistoryData(StandAssignment $assignment): void
    {
        DB::transaction(function () use ($assignment) {
            $assignment->delete();
            $this->historyService->deleteHistoryFor($assignment);
        });
    }
}
