<?php

namespace App\Allocator\Squawk;

interface SquawkAllocationInterface
{
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
