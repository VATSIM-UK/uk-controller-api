<?php

namespace App\Listeners\Stand;

use App\Events\StandUnassignedEvent;
use App\Models\Stand\StandAssignmentsHistory;

class MarkAssignmentDeletedOnUnassignment
{
    public function handle(StandUnassignedEvent $event) : bool
    {
        StandAssignmentsHistory::where('callsign', $event->getCallsign())->delete();
        return true;
    }
}
