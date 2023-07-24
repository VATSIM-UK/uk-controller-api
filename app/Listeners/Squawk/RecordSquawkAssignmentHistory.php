<?php

namespace App\Listeners\Squawk;

use App\Events\SquawkAssignmentEvent;
use App\Models\Squawk\SquawkAssignmentsHistory;
use Illuminate\Support\Facades\Auth;

class RecordSquawkAssignmentHistory
{
    /**
     * Handle any squawk allocation event
     *
     * @param SquawkAssignmentEvent $allocationEvent
     * @return bool
     */
    public function handle(SquawkAssignmentEvent $allocationEvent) : bool
    {
        SquawkAssignmentsHistory::create(
            [
                'callsign' => $allocationEvent->getAssignment()->getCallsign(),
                'code' => $allocationEvent->getAssignment()->getCode(),
                'type' => $allocationEvent->getAssignment()->getType(),
                'user_id' => !is_null(Auth::user()) ? Auth::user()->id : null,
            ]
        );
        return true;
    }
}
