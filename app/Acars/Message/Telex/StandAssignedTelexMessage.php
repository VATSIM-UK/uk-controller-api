<?php

namespace App\Acars\Message\Telex;

use App\Models\Stand\StandAssignment;

class StandAssignedTelexMessage implements TelexMessageInterface
{
    private StandAssignment $standAssignment;

    public function __construct(StandAssignment $standAssignment)
    {
        $this->standAssignment = $standAssignment;
    }

    public function getTarget(): string
    {
        return $this->standAssignment->callsign;
    }

    public function getBody(): string
    {
        return sprintf(
            "You have been provisionally assigned stand %s.\n\n" .
            "Safe landings.\n\n" .
            "VATSIM UK",
            $this->getStandAssignmentString()
        );
    }

    private function getStandAssignmentString(): string
    {
        $stand = $this->standAssignment->stand;
        return sprintf('%s/%s', $stand->airfield->code, $stand->identifier);
    }
}
