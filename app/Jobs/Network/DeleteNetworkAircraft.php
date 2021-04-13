<?php

namespace App\Jobs\Network;

use App\Models\Vatsim\NetworkAircraft;

class DeleteNetworkAircraft implements AircraftDisconnectedSubtask
{
    public function perform(NetworkAircraft $aircraft): void
    {
        $aircraft->delete();
    }
}
