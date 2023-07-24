<?php

namespace App\Listeners\Squawk;

use App\Events\SquawkUnassignedEvent;
use App\Models\Squawk\SquawkAssignmentsHistory;

class MarkAssignmentHistoryDeletedOnUnassignment
{
    public function handle(SquawkUnassignedEvent $event) : void
    {
        SquawkAssignmentsHistory::where('callsign', $event->getCallsign())->delete();
    }
}
