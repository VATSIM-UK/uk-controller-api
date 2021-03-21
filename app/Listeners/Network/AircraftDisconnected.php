<?php

namespace App\Listeners\Network;

use App\Events\NetworkAircraftDisconnectedEvent;
use App\Jobs\Hold\UnassignHoldOnDisconnect;
use App\Jobs\Network\DeleteNetworkAircraft;
use App\Jobs\Squawk\MarkAssignmentDeletedOnDisconnect;
use App\Jobs\Stand\TriggerUnassignmentOnDisconnect;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;

class AircraftDisconnected implements ShouldQueue
{
    public function handle(NetworkAircraftDisconnectedEvent $event): bool
    {
        $aircraft = $event->getAircraft();
        Bus::chain(
            [
                new TriggerUnassignmentOnDisconnect($aircraft),
                new UnassignHoldOnDisconnect($aircraft),
                new MarkAssignmentDeletedOnDisconnect($aircraft),
                new DeleteNetworkAircraft($aircraft),
            ]
        )->dispatch();
        return true;
    }
}
