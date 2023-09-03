<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Collection;

/**
 * An interface for stand allocators that provide stand allocations in some sort of ranked
 * order. This interface can be used to display rank predictions to users to help with assignment debugging.
 */
interface RankableArrivalStandAllocator
{
    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection;
}
