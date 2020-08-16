<?php

namespace App\Listeners\Stand;

use App\Events\StandAssignedEvent;
use App\Models\Stand\StandAssignmentsHistory;
use Illuminate\Support\Facades\Auth;

class RecordStandAssignmentHistory
{
    /**
     * Handle any stand allocation event
     *
     * @param StandAssignedEvent $assignedEvent
     * @return bool
     */
    public function handle(StandAssignedEvent $assignedEvent) : bool
    {
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
