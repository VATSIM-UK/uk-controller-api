<?php

namespace App\Listeners\Squawk;

use App\Events\SquawkUnassignedEvent;
use App\Models\Squawk\SquawkAssignmentsHistory;

class MarkAssignmentHistoryDeletedOnUnassignment
{
    public function handle(SquawkUnassignedEvent $event) : bool
    {
        SquawkAssignmentsHistory::where('callsign', $event->getCallsign())->delete();
        return true;
    }
}
