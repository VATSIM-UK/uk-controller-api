<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;

interface ArrivalStandAllocatorInterface
{
    /**
     * If called, should allocate a stand to an aircraft and return it. If an allocation
     * is not possible, should return null.
     *
     * @param NetworkAircraft $aircraft
     * @return Stand|null
     */
    public function allocate(NetworkAircraft $aircraft): ?Stand;
}
