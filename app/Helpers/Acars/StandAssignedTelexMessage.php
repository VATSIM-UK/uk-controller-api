<?php

namespace App\Helpers\Acars;

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

    public function getMessage(): string
    {
        return sprintf(
            "VATSIM UK Stand Assignment\n" .
            "--------------------------\n\n" .
            "You have been provisionally assigned stand %s.\n\n" .
            "This message is non-binding, subject to change at controller discretion " .
            "and is for planning purposes only.",
            $this->getStandAssignmentString()
        );
    }

    private function getStandAssignmentString(): string
    {
        $stand = $this->standAssignment->stand;
        return sprintf('%s/%s', $stand->identifier, $stand->airfield->code);
    }
}
