<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;

interface ArrivalStandAllocatorInterface
{
    /**
     * Returns the id of the stand to allocate, or null if one is not found.
     */
    public function allocate(NetworkAircraft $aircraft): ?int;
}
