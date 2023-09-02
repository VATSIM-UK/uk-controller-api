<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;

/**
 * An interface for any allocator that allocates stands for aircraft.
 * 
 * It is expected that the allocator will return the ID of the stand that it has allocated.
 */
interface ArrivalStandAllocator
{
    public function allocate(NetworkAircraft $aircraft): ?int;
}
