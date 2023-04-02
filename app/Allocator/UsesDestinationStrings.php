<?php

namespace App\Allocator;

use App\Models\Vatsim\NetworkAircraft;

trait UsesDestinationStrings
{
    private function getDestinationStrings(NetworkAircraft|string $aircraftOrDestination): array
    {
        $destination = $aircraftOrDestination instanceof NetworkAircraft
            ? $aircraftOrDestination->planned_depairport
            : $aircraftOrDestination;

        return [
            substr($destination, 0, 1),
            substr($destination, 0, 2),
            substr($destination, 0, 3),
            $destination
        ];
    }
}
