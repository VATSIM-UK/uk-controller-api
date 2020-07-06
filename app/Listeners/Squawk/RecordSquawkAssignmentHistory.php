<?php

namespace App\Listeners\Squawk;

use App\Events\SquawkAssignmentEvent;
use App\Models\Squawks\AllocationHistory;

/**
 * Class RecordSquawkAllocationHistory
 * @package App\Listeners
 */
class RecordSquawkAllocationHistory
{
    /**
     * Handle any squawk allocation event
     *
     * @param SquawkAssignmentEvent $allocationEvent
     * @return bool
     */
    public function handle(SquawkAssignmentEvent $allocationEvent) : bool
    {
        AllocationHistory::create(
            $allocationEvent->allocation()
        );

        return false;
    }
}
