<?php

namespace App\Allocator\Squawk;

interface SquawkAssignmentInterface
{
    /**
     * Returns the callsign for the assignment.
     */
    public function getCallsign(): string;

    /**
     * Returns the squawk code that has been allocated.
     */
    public function getCode(): string;

    /**
     * Returns the type of allocation made.
     */
    public function getType(): string;
}
