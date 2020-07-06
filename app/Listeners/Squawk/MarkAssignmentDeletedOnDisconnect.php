<?php

namespace App\Listeners\Squawk;

use App\Events\NetworkAircraftDisconnectedEvent;
use App\Models\Squawk\SquawkAssignmentsHistory;

class MarkAssignmentDeletedOnDisconnect
{
    public function handle(NetworkAircraftDisconnectedEvent $event) : bool
    {
        SquawkAssignmentsHistory::where('callsign', $event->getAircraft()->callsign)->delete();
        return true;
    }
}
