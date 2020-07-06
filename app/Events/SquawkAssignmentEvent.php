<?php


namespace App\Events;

use App\Models\Squawks\Allocation;

class SquawkAllocationEvent
{
    /**
     * The squawk allocation that has just been created.
     *
     * @var Allocation
     */
    private $allocation;

    /**
     * SquawkAllocationEventTest constructor.
     * @param Allocation $allocation
     */
    public function __construct(Allocation $allocation)
    {
        $this->allocation = $allocation;
    }

    /**
     * Returns the array representation of the
     * allocation. Adds whether or not it was a new
     * allocation.
     *
     * @return array
     */
    public function allocation() : array
    {
        return array_merge(
            $this->allocation->toArray(),
            [
                'new' => $this->allocation->wasRecentlyCreated,
            ]
        );
    }
}
