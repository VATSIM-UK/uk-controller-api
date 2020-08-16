<?php

namespace App\Listeners\Stand;

use App\Events\StandAssignedEvent;
use App\Models\Stand\StandAssignmentsHistory;
use Illuminate\Support\Facades\Auth;

class RecordStandAssignmentHistory
{
    public function handle(StandAssignedEvent $assignedEvent) : bool
    {
        // Mark any current assignments as deleted and create a new history item
        StandAssignmentsHistory::where('callsign', $assignedEvent->getStandAssignment()->callsign)->delete();
        StandAssignmentsHistory::create(
            [
                'callsign' => $assignedEvent->getStandAssignment()->callsign,
                'stand_id' => $assignedEvent->getStandAssignment()->stand_id,
                'user_id' => !is_null(Auth::user()) ? Auth::user()->id : null,
            ]
        );
        return true;
    }
}
