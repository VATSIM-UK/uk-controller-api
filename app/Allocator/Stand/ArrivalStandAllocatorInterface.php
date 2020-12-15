<?php

namespace App\Allocator\Stand;

use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;

interface ArrivalStandAllocatorInterface
{
    /**
     * If called, should allocate a stand to an aircraft and return it. If an allocation
     * is not possible, should return null.
     *
     * @param NetworkAircraft $aircraft
     * @return StandAssignment|null
     */
    public function allocate(NetworkAircraft $aircraft): ?StandAssignment;
}
