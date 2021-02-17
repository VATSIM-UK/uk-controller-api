<?php

namespace App\Events;

use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;

class StandOccupiedEvent
{
    private NetworkAircraft $aircraft;
    private Stand $stand;

    public function __construct(NetworkAircraft $aircraft, Stand $stand)
    {
        $this->aircraft = $aircraft;
        $this->stand = $stand;
    }

    public function getAircraft(): NetworkAircraft
    {
        return $this->aircraft;
    }

    public function getStand(): Stand
    {
        return $this->stand;
    }
}
