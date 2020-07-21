<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\SquawkAssignmentInterface;

interface GeneralSquawkAllocatorInterface
{
    /**
     * Returns whether or not the code is allocated.
     *
     * @param string $code
     * @param string $callsign
     * @return bool
     */
    public function assignToCallsign(string $code, string $callsign): ?SquawkAssignmentInterface;
}
