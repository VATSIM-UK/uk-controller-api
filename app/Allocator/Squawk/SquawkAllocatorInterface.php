<?php

namespace App\Allocator\Squawk;

interface SquawkAllocatorInterface
{
    /**
     * Returns whether or not the allocator can allocate
     * a squawk for the given category.
     *
     * @param string $category
     * @return bool
     */
    public function canAllocateForCategory(string $category): bool;

    /**
     * Allocates a squawk, given the aircrafts callsign and any other
     * pertinent details about its flight.
     *
     * @param string $callsign
     * @param array $details
     * @return SquawkAssignmentInterface|null
     */
    public function allocate(string $callsign, array $details): ?SquawkAssignmentInterface;

    /**
     * Deletes the allocation for a given callsign.
     *
     * @param string $callsign
     * @return mixed
     */
    public function delete(string $callsign): void;

    /**
     * Fetches the allocation for a given callsign.
     *
     * @param string $callsign
     * @return mixed
     */
    public function fetch(string $callsign): ?SquawkAssignmentInterface;
}
