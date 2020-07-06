<?php

namespace App\Events;

use App\Allocator\Squawk\SquawkAssignmentInterface;

class SquawkAssignmentEvent
{
    /**
     * @var SquawkAssignmentInterface
     */
    private $assignment;

    public function __construct(SquawkAssignmentInterface $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * @return SquawkAssignmentInterface
     */
    public function getAssignment(): SquawkAssignmentInterface
    {
        return $this->assignment;
    }
}
