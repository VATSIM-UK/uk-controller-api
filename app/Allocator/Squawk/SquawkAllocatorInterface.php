<?php

namespace App\Allocator\Squawk;

interface SquawkAllocatorInterface
{
    /**
     * Allocates a squawk, given the aircrafts callsign and any other
     * pertinent details about its flight.
     */
    public function allocate(string $callsign, array $details): ?SquawkAssignmentInterface;
}
