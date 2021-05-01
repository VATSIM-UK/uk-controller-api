<?php

namespace App\Allocator\Squawk;

interface SquawkAllocatorInterface
{
    /**
     * Allocates a squawk, given the aircrafts callsign and any other
     * pertinent details about its flight.
     *
     * @param string $callsign
     * @param array $details
     * @return SquawkAssignmentInterface|null
     */
    public function allocate(string $callsign, array $details): ?SquawkAssignmentInterface;
}
