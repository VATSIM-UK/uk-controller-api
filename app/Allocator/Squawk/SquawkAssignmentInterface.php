<?php

namespace App\Allocator\Squawk;

interface SquawkAssignmentInterface
{
    /**
     * Returns the callsign for the assignment.
     *
     * @return string
     */
    public function getCallsign(): string;

    /**
     * Returns the squawk code that has been allocated.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Returns the type of allocation made.
     *
     * @return string
     */
    public function getType(): string;
}
