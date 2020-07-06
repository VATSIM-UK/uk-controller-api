<?php

namespace App\Listeners\Squawk;

use App\Events\SquawkUnassignedEvent;
use App\Models\Squawk\SquawkAssignmentsHistory;

class MarkAssignmentDeletedOnUnassignment
{
    public function handle(SquawkUnassignedEvent $event) : bool
    {
        SquawkAssignmentsHistory::where('callsign', $event->getDeletedAssignment()->getCallsign())->delete();
        return true;
    }
}
