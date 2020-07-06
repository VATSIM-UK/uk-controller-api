<?php

namespace App\Services;

use App\Allocator\Squawk\SquawkAllocatorInterface;
use App\Allocator\Squawk\SquawkAssignmentCategories;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Events\SquawkUnassignedEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service for converting squawk requests into data arrays that can be returned as
 * a response.
 *
 * Class SquawkServiceProvider
 * @package App\Providers
 */
class SquawkService
{
    /**
     * @var SquawkAllocatorInterface[]
     */
    private $allocators;

    /**
     * Constructor
     *
     * @param SquawkAllocatorInterface[] $allocators
     */
    public function __construct(
        array $allocators
    ) {
        $this->allocators = $allocators;
    }

    /**
     * De-allocates a squawk for the given callsign, freeing up the squawk for use again.
     *
     * @param string $callsign The callsign to deallocate for
     * @return bool True if successful, false otherwise.
     */
    public function deleteSquawkAssignment(string $callsign): bool
    {
        foreach ($this->allocators as $allocator) {
            if ($allocator->delete($callsign)) {
                event(new SquawkUnassignedEvent($callsign));
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the squawk allocated to the given callsign.
     *
     * @param string $callsign The callsign to check
     * @return SquawkAssignmentInterface|null
     */
    public function getAssignedSquawk(string $callsign): ?SquawkAssignmentInterface
    {
        $assignment = null;
        foreach ($this->allocators as $allocator) {
            if ($assignment = $allocator->fetch($callsign)) {
                break;
            }
        }
        return $assignment;
    }

    /**
     * Get a local (unit or airfield specific squawk) for a given aircraft.
     *
     * @param string $callsign Aircraft callsign
     * @param string $unit The ATC unit to search for squawks in
     * @param string $rules The flight rules
     * @return SquawkAssignmentInterface|null
     */
    public function assignLocalSquawk(string $callsign, string $unit, string $rules): ?SquawkAssignmentInterface
    {
        $assignment = null;
        DB::transaction(function() use ($callsign, $unit, $rules, &$assignment) {
            $this->deleteSquawkAssignment($callsign);

            foreach ($this->allocators as $allocator) {
                if (
                    $allocator->canAllocateForCategory(SquawkAssignmentCategories::CATEGORY_LOCAL) &&
                    $assignment = $allocator->allocate($callsign, ['unit' => $unit, 'rules' => $rules])
                ) {
                    return;
                }
            }
        });

        return $assignment;
    }

    /**
     * Searches for a general squawk based on origin and destination and allocates if found.
     *
     * @param string $callsign The aircraft's callsign
     * @param string $origin The origin ICAO
     * @param string $destination The destination ICAO
     * @return SquawkAssignmentInterface
     */
    public function assignGeneralSquawk(
        string $callsign,
        string $origin,
        string $destination
    ): ?SquawkAssignmentInterface {

        $assignment = null;
        DB::transaction(function () use ($callsign, $origin, $destination, &$assignment) {
            $this->deleteSquawkAssignment($callsign);
            foreach ($this->allocators as $allocator) {
                if (
                    $allocator->canAllocateForCategory(SquawkAssignmentCategories::CATEGORY_GENERAL) &&
                    $assignment = $allocator->allocate($callsign, ['origin' => $origin, 'destination' => $destination])
                ) {
                    return $assignment;
                }
            }
        });

        return $assignment;
    }

    /**
     * @return string[]
     */
    public function getAllocatorPreference(): array
    {
        return array_map(function (SquawkAllocatorInterface $allocator) {
            return get_class($allocator);
        }, $this->allocators);
    }
}
