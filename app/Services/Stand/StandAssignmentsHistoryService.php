<?php

namespace App\Services\Stand;

use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandAssignmentsHistory;
use App\Models\Stand\StandRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StandAssignmentsHistoryService implements RecordsAssignmentHistory
{
    private readonly StandRequestService $standRequestService;

    public function __construct(StandRequestService $standRequestService)
    {
        $this->standRequestService = $standRequestService;
    }

    public function deleteHistoryFor(StandAssignment $target): void
    {
        StandAssignmentsHistory::where(
            'callsign',
            $target->callsign
        )->delete();
    }

    public function createHistoryItem(StandAssignmentContext $context): void
    {
        DB::transaction(function () use ($context)
        {
            $assignment = $context->assignment;
            $this->deleteHistoryFor($assignment);
            StandAssignmentsHistory::create(
                [
                    'callsign' => $assignment->callsign,
                    'stand_id' => $assignment->stand_id,
                    'type' => $context->assignmentType,
                    'user_id' => !is_null(Auth::user()) ? Auth::user()->id : null,
                    'context' => $this->generateContext($context),
                ]
            );
        });
    }

    private function generateContext(StandAssignmentContext $context): array
    {
        return [
            'aircraft_departure_airfield' => $context->aircraft->planned_depairport,
            'aircraft_arrival_airfield' => $context->aircraft->planned_destairport,
            'aircraft_type' => $context->aircraft->planned_aircraft_short,
            'removed_assignments' => $context->removedAssignments->map(
                function (StandAssignment $assignment)
                {
                    return [
                        'callsign' => $assignment->callsign,
                        'stand' => $assignment->stand->identifier,
                    ];
                }
            ),
            'occupied_stands' => Stand::where('airfield_id', $context->assignment->stand->airfield_id)
                ->where('id', '<>', $context->assignment->stand_id)
                ->whereHas('occupier')
                ->orderBy('stands.id')
                ->get()
                ->map(fn(Stand $stand) => $stand->identifier),
            'assigned_stands' => Stand::where('airfield_id', $context->assignment->stand->airfield_id)
                ->where('id', '<>', $context->assignment->stand_id)
                ->whereHas('assignment')
                ->orderBy('stands.id')
                ->get()
                ->map(fn(Stand $stand) => $stand->identifier),
            'flightplan_remarks' => $context->aircraft->remarks,
            'requested_stand' => $this->standRequestService->activeRequestForAircraft($context->aircraft)?->stand->identifier,
            'other_requested_stands' => $this->standRequestService->allActiveStandRequestsForAirfield($context->assignment->stand->airfield->code)
                ->filter(fn(StandRequest $request) => $request->stand_id !== $context->assignment->stand_id)
                ->map(fn(StandRequest $request) => $request->stand->identifier)
                ->values()
                ->toArray(),
        ];
    }
}
