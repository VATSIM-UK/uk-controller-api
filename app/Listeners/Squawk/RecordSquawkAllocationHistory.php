<?php

namespace App\Listeners\Squawk;

use App\Events\SquawkAllocationEvent;
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
     * @param SquawkAllocationEvent $allocationEvent
     * @return bool
     */
    public function handle(SquawkAllocationEvent $allocationEvent) : bool
    {
        AllocationHistory::create(
            $allocationEvent->allocation()
        );

        return false;
    }
}
