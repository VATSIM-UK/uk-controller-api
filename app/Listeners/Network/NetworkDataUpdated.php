<?php

namespace App\Listeners\Network;

use App\Jobs\Hold\DetectProximityToHolds;
use App\Jobs\Hold\RemoveAssignmentsForAircraftLeavingHold;
use App\Jobs\Prenote\CancelMessagesForDepartedAircraft;
use App\Jobs\Release\Departure\CancelRequestsForDepartedAircraft;
use App\Jobs\Squawk\ReserveActiveSquawks;
use App\Jobs\Stand\AssignStandsForArrival;
use App\Jobs\Stand\AssignStandsForDeparture;
use App\Jobs\Stand\OccupyStands;
use App\Jobs\Stand\RemoveDisconnectedArrivalStands;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;

class NetworkDataUpdated implements ShouldQueue, ShouldBeUnique
{
    // This listener should be unique for 5 minutes.
    public $uniqueFor = 300;

    public function handle()
    {
        Bus::chain(
            [
                new OccupyStands(),
                new AssignStandsForDeparture(),
                new RemoveDisconnectedArrivalStands(),
                new AssignStandsForArrival(),
                new ReserveActiveSquawks(),
                new RemoveAssignmentsForAircraftLeavingHold(),
                new CancelRequestsForDepartedAircraft(),
                new CancelMessagesForDepartedAircraft(),
                new DetectProximityToHolds(),
            ]
        )->dispatch();
        return true;
    }
}
