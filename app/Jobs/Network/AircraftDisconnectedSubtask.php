<?php

namespace App\Jobs\Network;

use App\Models\Vatsim\NetworkAircraft;

interface AircraftDisconnectedSubtask
{
    /**
     * Should perform whatever task needs doing when expiring a network aircraft.
     */
    public function perform(NetworkAircraft $aircraft): void;
}
