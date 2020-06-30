<?php

namespace App\Events;

use App\Models\Vatsim\NetworkAircraft;

class NetworkAircraftDisconnectedEvent
{
    /**
     * @var NetworkAircraft
     */
    private $aircraft;

    public function __construct(NetworkAircraft $aircraft)
    {
        $this->aircraft = $aircraft;
    }

    /**
     * @return NetworkAircraft
     */
    public function getAircraft(): NetworkAircraft
    {
        return $this->aircraft;
    }
}
