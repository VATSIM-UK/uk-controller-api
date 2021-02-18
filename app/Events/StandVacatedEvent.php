<?php

namespace App\Events;

use App\Models\Vatsim\NetworkAircraft;

class StandVacatedEvent
{
    private NetworkAircraft $aircraft;

    public function __construct(NetworkAircraft $aircraft)
    {
        $this->aircraft = $aircraft;
    }

    public function getAircraft(): NetworkAircraft
    {
        return $this->aircraft;
    }
}
