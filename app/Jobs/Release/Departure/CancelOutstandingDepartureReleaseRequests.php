<?php

namespace App\Jobs\Release\Departure;

use App\Events\DepartureReleaseRequestCancelledEvent;
use App\Jobs\Network\AircraftDisconnectedSubtask;
use App\Models\Release\Departure\DepartureReleaseRequest;
use App\Models\Vatsim\NetworkAircraft;

class CancelOutstandingDepartureReleaseRequests implements AircraftDisconnectedSubtask
{
    public function perform(NetworkAircraft $aircraft): void
    {
        DepartureReleaseRequest::where('callsign', $aircraft->callsign)->each(
                function (DepartureReleaseRequest $request) {
                    $request->delete();
                    event(new DepartureReleaseRequestCancelledEvent($request));
                }
            );
    }
}
