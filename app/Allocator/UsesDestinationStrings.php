<?php

namespace App\Allocator;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Str;

trait UsesDestinationStrings
{
    private function getDestinationStrings(NetworkAircraft|string $aircraftOrDestination): array
    {
        $destination = $aircraftOrDestination instanceof NetworkAircraft
            ? $aircraftOrDestination->planned_depairport
            : $aircraftOrDestination;

        return [
            Str::substr($destination, 0, 1),
            Str::substr($destination, 0, 2),
            Str::substr($destination, 0, 3),
            $destination
        ];
    }
}
