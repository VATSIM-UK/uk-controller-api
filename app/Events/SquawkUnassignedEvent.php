<?php

namespace App\Events;

use App\Allocator\Squawk\SquawkAssignmentInterface;

class SquawkUnassignedEvent
{
    /**
     * @var SquawkAssignmentInterface
     */
    private $deletedAssignment;

    public function __construct(SquawkAssignmentInterface $deletedAssignment)
    {
        $this->deletedAssignment = $deletedAssignment;
    }

    /**
     * @return SquawkAssignmentInterface
     */
    public function getDeletedAssignment(): SquawkAssignmentInterface
    {
        return $this->deletedAssignment;
    }
}
