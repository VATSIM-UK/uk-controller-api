<?php

namespace App\Services;

use App\Allocator\Squawk\SquawkAllocatorInterface;
use App\Allocator\Squawk\SquawkAssignmentCategories;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Events\SquawkAssignmentEvent;
use App\Events\SquawkUnassignedEvent;
use App\Models\Squawk\Reserved\ReservedSquawkCode;
use App\Models\Vatsim\NetworkAircraft;
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
        return $this->assignSquawk(
            $callsign,
            SquawkAssignmentCategories::LOCAL,
            ['unit' => $unit, 'rules' => $rules]
        );
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
        return $this->assignSquawk(
            $callsign,
            SquawkAssignmentCategories::GENERAL,
            ['origin' => $origin, 'destination' => $destination]
        );
    }

    private function assignSquawk(string $callsign, string $category, array $details): ?SquawkAssignmentInterface
    {
        $assignment = null;
        DB::transaction(function () use ($callsign, $category, $details, &$assignment) {
            $this->deleteSquawkAssignment($callsign);
            foreach ($this->allocators as $allocator) {
                if (
                    $allocator->canAllocateForCategory($category) &&
                    $assignment = $allocator->allocate($callsign, $details)
                ) {
                    return;
                }
            }
        });

        // If a squawk has been assigned, let the rest of the app know so it can be audited etc
        if (!is_null($assignment)) {
            event(new SquawkAssignmentEvent($assignment));
        }

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

    public function reserveSquawkForAircraft(string $callsign): void
    {
        DB::transaction(function () use ($callsign) {
            $aircraft = NetworkAircraft::lockForUpdate()->find($callsign);

            $currentAssignment = null;
            $responsibleAllocator = null;
            foreach ($this->allocators as $allocator) {
                if (
                    $allocator->canAllocateForCategory(SquawkAssignmentCategories::GENERAL) &&
                    ($currentAssignment = $allocator->fetch($callsign))
                )  {
                    // If the current assignment is what they're squawking, great, nothing to do.
                    if ($currentAssignment->getCode() === $aircraft->squawk) {
                        return;
                    }

                    $responsibleAllocator = $allocator;
                    break;
                }
            }

            // The current squawk has changed from what's assigned, so delete reallocate
            $responsibleAllocator->delete($callsign);
            event(new SquawkUnassignedEvent($callsign));

            // The aircraft is squawking a reserved code, so we can't reserve it.
            if (ReservedSquawkCode::where('code', $aircraft->squawk)->first()) {
                return;
            }

            $allocationDetails = [
                'callsign' => $callsign,
                'origin' => $aircraft->planned_depairport,
                'destination' => $aircraft->planned_destairport,
            ];
            $newAssignment = null;
            foreach ($this->allocators as $allocator) {
                if (
                    $allocator->canAllocateForCategory(SquawkAssignmentCategories::GENERAL) &&
                    $newAssignment = $allocator->allocate($callsign, $allocationDetails)
                ) {
                    break;
                }
            }

            // Register the new assignment if we can
            if (is_null($newAssignment)) {
                return;
            }

            event(new SquawkAssignmentEvent($newAssignment));
        });
    }
}
