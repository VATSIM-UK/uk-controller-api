<?php

namespace App\Services;

use App\Allocator\Squawk\SquawkAllocatorInterface;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Events\SquawkAssignmentEvent;
use App\Events\SquawkUnassignedEvent;
use App\Models\Squawk\Reserved\NonAssignableSquawkCode;
use App\Models\Squawk\SquawkAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Get all the aircraft that:
     *
     * 1. Either don't have a squawk code assigned, or it's different to what they're squawking
     * 2. Haven't changed their transponder in a while
     * 3. Are not squawk a forbidden code
     * 4. Are not squawking a code that's already assigned
     *
     * @return Collection|NetworkAircraft[]
     */
    private function getSquawksToAssign(): Collection
    {
        return NetworkAircraft::leftJoin('squawk_assignments', 'squawk_assignments.callsign', '=', 'network_aircraft.callsign')
            ->where(function (Builder $subQuery) {
                $subQuery->whereRaw('`squawk_assignments`.`code` <> `network_aircraft`.`transponder`')
                    ->orWhereNull('squawk_assignments.code');
            })
            ->where('transponder_last_updated_at', '<', Carbon::now()->subMinutes(2))
            ->whereNotIn('network_aircraft.transponder', NonAssignableSquawkCode::all()->pluck('code'))
            ->whereNotIn('network_aircraft.transponder', SquawkAssignment::all()->pluck('code'))
            ->select('network_aircraft.*')
            ->get();
    }

    public function reserveActiveSquawks(): void
    {
        $squawksAlreadyReserved = [];
        foreach ($this->getSquawksToAssign() as $aircraft) {
            if (array_search($aircraft->transponder, $squawksAlreadyReserved) !== false) {
                continue;
            }

            $assignment = SquawkAssignment::updateOrCreate(
                [
                    'callsign' => $aircraft->callsign,
                ],
                [
                    'code' => $aircraft->transponder,
                    'assignment_type' => 'NON_UKCP',
                ]
            );
            event(new SquawkAssignmentEvent($assignment));
            $squawksAlreadyReserved[] = $aircraft->transponder;
        }
    }
}
