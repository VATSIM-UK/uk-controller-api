<?php


namespace App\Listeners\Hold;


use App\Events\HoldUnassignedEvent;
use App\Models\Vatsim\NetworkAircraft;

class UnassignHoldOnDisconnect
{
    public function handle(NetworkAircraft $aircraft) : bool
    {
        event(new HoldUnassignedEvent($aircraft));
        return false;
    }
}
