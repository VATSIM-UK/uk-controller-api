<?php

namespace App\Events;

class SquawkUnassignedEvent
{
    /**
     * @var string
     */
    private $callsign;

    public function __construct(string $callsign)
    {
        $this->callsign = $callsign;
    }

    public function getCallsign(): string
    {
        return $this->callsign;
    }
}
