<?php

namespace App\Services;

use App\Allocator\Squawk\SquawkAllocatorInterface;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Events\SquawkAssignmentEvent;
use App\Events\SquawkUnassignedEvent;
use App\Models\Squawk\Reserved\NonAssignableSquawkCode;
use App\Models\Squawk\SquawkAssignment;
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
    private array $generalAllocators;

    /**
     * @var SquawkAllocatorInterface[]
     */
    private array $localAllocators;

    /**
     * Constructor
     *
     * @param SquawkAllocatorInterface[] $generalAllocators
     * @param SquawkAllocatorInterface[] $localAllocators
     */
    public function __construct(
        array $generalAllocators,
        array $localAllocators
    ) {
        $this->generalAllocators = $generalAllocators;
        $this->localAllocators = $localAllocators;
    }

    /**
     * De-allocates a squawk for the given callsign, freeing up the squawk for use again.
     *
     * @param string $callsign The callsign to deallocate for
     * @return bool True if successful, false otherwise.
     */
    public function deleteSquawkAssignment(string $callsign): bool
    {
        if ($destroyed = SquawkAssignment::destroy($callsign)) {
            event(new SquawkUnassignedEvent($callsign));
        }

        return $destroyed;
    }

    /**
     * Returns the squawk allocated to the given callsign.
     *
     * @param string $callsign The callsign to check
     * @return SquawkAssignmentInterface|null
     */
    public function getAssignedSquawk(string $callsign): ?SquawkAssignmentInterface
    {
        return SquawkAssignment::find($callsign);
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
            $this->localAllocators,
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
            $this->generalAllocators,
            ['origin' => $origin, 'destination' => $destination]
        );
    }

    private function assignSquawk(string $callsign, array $allocators, array $details): ?SquawkAssignmentInterface
    {
        $assignment = null;
        DB::transaction(
            function () use ($callsign, $allocators, $details, &$assignment) {
                $this->deleteSquawkAssignment($callsign);
                foreach ($allocators as $allocator) {
                    if ($assignment = $allocator->allocate($callsign, $details)) {
                        return;
                    }
                }
            }
        );

        // If a squawk has been assigned, let the rest of the app know so it can be audited etc
        if (!is_null($assignment)) {
            event(new SquawkAssignmentEvent($assignment));
        }

        return $assignment;
    }

    /**
     * @return string[]
     */
    public function getGeneralAllocatorPreference(): array
    {
        return array_map(
            function (SquawkAllocatorInterface $allocator) {
                return get_class($allocator);
            },
            $this->generalAllocators
        );
    }

    /**
     * @return string[]
     */
    public function getLocalAllocatorPreference(): array
    {
        return array_map(
            function (SquawkAllocatorInterface $allocator) {
                return get_class($allocator);
            },
            $this->localAllocators
        );
    }

    public function reserveSquawkForAircraft(string $callsign): ?SquawkAssignmentInterface
    {
        $assignment = null;
        DB::transaction(
            function () use ($callsign, &$assignment) {
                $aircraft = NetworkAircraft::find($callsign);
                if (!$aircraft) {
                    return;
                }

                foreach ($this->generalAllocators as $allocator) {
                    if ($allocator->fetch($callsign)) {
                        // The current squawk has changed from what's assigned, so delete it
                        $allocator->delete($callsign);
                        event(new SquawkUnassignedEvent($callsign));
                        break;
                    }
                }

                // The aircraft is squawking a reserved code, so we can't reserve it.
                if ($this->squawkIsNotAssignable($aircraft->squawk)) {
                    return;
                }

                // Try and do a new allocation
                $assignment = $this->assignCodeToAircraft($callsign, $aircraft->transponder);
            }
        );

        return $assignment;
    }

    /**
     * Returns if a squawk is a non-allocatable code code.
     *
     * @param string $code
     * @return bool
     */
    private function squawkIsNotAssignable(string $code): bool
    {
        return NonAssignableSquawkCode::where('code', $code)->exists();
    }

    /**
     * Try to assign a specific code to an aircraft
     *
     * @param string $callsign
     * @param string $code
     * @return SquawkAssignmentInterface|null
     */
    private function assignCodeToAircraft(string $callsign, string $code): ?SquawkAssignmentInterface
    {
        foreach ($this->generalAllocators as $allocator) {
            if ($newAssignment = $allocator->assignToCallsign($code, $callsign)) {
                event(new SquawkAssignmentEvent($newAssignment));
                return $newAssignment;
            }
        }

        return null;
    }

    public function reserveSquawksInFirProximity(): void
    {

    }
}
