<?php

namespace App\Listeners\Squawk;

use App\Events\SquawkUnassignedEvent;
use App\Models\Squawk\SquawkAssignmentsHistory;
use Illuminate\Contracts\Queue\ShouldQueue;

class MarkAssignmentHistoryDeletedOnUnassignment implements ShouldQueue
{
    public function handle(SquawkUnassignedEvent $event) : bool
    {
        SquawkAssignmentsHistory::where('callsign', $event->getCallsign())->delete();
        return true;
    }
}
