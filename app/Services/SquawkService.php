<?php

namespace App\Services;

use App\Exceptions\SquawkNotAllocatedException;
use App\Exceptions\SquawkNotAssignedException;
use App\Helpers\Squawks\SquawkAllocation;
use App\Libraries\GeneralSquawkRuleGenerator;
use App\Models\Squawks\Allocation;
use App\Models\Squawks\Range;
use App\Models\Squawks\SquawkGeneral;
use App\Models\Squawks\SquawkUnit;
use InvalidArgumentException;

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
     * Constant deemed to mean Any Flight Rules.
     *
     * @var String
     */
    const RULES_ANY = 'A';

    /**
     * Converts origin and destination to rules to check
     * when allocating general squawks.
     *
     * @var GeneralSquawkRuleGenerator
     */
    private $rulesGenerator;

    /**
     * For creating and auditing squawk allocations.
     *
     * @var SquawkAllocationService
     */
    private $squawkAllocationService;

    /**
     * Constructor
     *
     * @param GeneralSquawkRuleGenerator $rulesGenerator
     * @param SquawkAllocationService $squawkAllocationService
     */
    public function __construct(
        GeneralSquawkRuleGenerator $rulesGenerator,
        SquawkAllocationService $squawkAllocationService
    ) {
        $this->rulesGenerator = $rulesGenerator;
        $this->squawkAllocationService = $squawkAllocationService;
    }

    /**
     * De-allocates a squawk for the given callsign, freeing up the squawk for use again.
     *
     * @param string $callsign The callsign to deallocate for
     * @return bool True if successful, false otherwise.
     */
    public function deleteSquawkAssignment(string $callsign) : bool
    {
        return (Allocation::where(['callsign' => $callsign])->delete() === 1) ? true : false;
    }

    /**
     * Returns the squawk allocated to the given callsign.
     *
     * @param string $callsign The callsign to check
     * @throws SquawkNotAssignedException If no squawk is assigned
     * @return string
     */
    public function getAssignedSquawk(string $callsign) : SquawkAllocation
    {
        $allocation = Allocation::where('callsign', '=', $callsign)->first();

        if (!$allocation) {
            throw new SquawkNotAssignedException("Squawk assignment not found for " . $callsign);
        }

        return new SquawkAllocation($allocation->squawk, false);
    }

    /**
     * Get a local (unit or airfield specific squawk) for a given aircraft.
     *
     * @param string $callsign Aircraft callsign
     * @param string $unit The ATC unit to search for squawks in
     * @param string $rules The flight rules
     * @return SquawkAllocation If squawk found and allocated
     * @throws SquawkNotAllocatedException If a squawk cannot be allocated.
     */
    public function assignLocalSquawk(string $callsign, string $unit, string $rules) : SquawkAllocation
    {
        // Try to find rule specific ranges first, otherwise use general ones
        $unit = SquawkUnit::where('unit', $unit)->first();
        if (!$unit) {
            throw new InvalidArgumentException('Unit not found');
        }

        if ($rules === 'S') {
            $rules = 'V';
        }

        $allRanges = $unit->ranges;
        $ruleSpecificRanges = $allRanges->where('rules', $rules)->shuffle();
        $rangesToUse = ($ruleSpecificRanges->isNotEmpty())
            ? $ruleSpecificRanges : $allRanges->where('rules', self::RULES_ANY)->shuffle();

        // Allocate a squawk from a random applicable range
        if ($rangesToUse && $rangesToUse->isNotEmpty()) {
            foreach ($rangesToUse as $localSquawkRange) {
                // Check the applicable ranges for a squawk and see if we can find one.
                $squawk = $this->searchForSquawkInRange($localSquawkRange);
                if ($squawk !== false) {
                    return $this->squawkAllocationService->createOrUpdateAllocation($callsign, $squawk);
                }
            }
        }

        // Throw an exception if we can't find a squawk.
        throw new SquawkNotAllocatedException('Unable to allocate local squawk for ' . $callsign);
    }

    /**
     * Searches for a general squawk based on origin and destination and allocates if found.
     *
     * @param string $callsign The aircraft's callsign
     * @param string $origin The origin ICAO
     * @param string $destination The destination ICAO
     * @return SquawkAllocation If squawk found and allocated
     * @throws SquawkNotAllocatedException If the squawk isn't found.
     */
    public function assignGeneralSquawk(string $callsign, string $origin, string $destination) : SquawkAllocation
    {
        $searches = $this->rulesGenerator->generateRules($origin, $destination);
        foreach ($searches as $search) {
            $ranges = SquawkGeneral::where($search)->get()->shuffle();

            if ($ranges && $ranges->isNotEmpty()) {
                foreach ($ranges as $generalSquawkRanges) {
                    $possibleRanges = $generalSquawkRanges->ranges->shuffle();
                    foreach ($possibleRanges as $possibleRange) {
                        // Check the applicable ranges for a squawk and see if we can find one.
                        $squawk = $this->searchForSquawkInRange($possibleRange);
                        if ($squawk !== false) {
                            return $this->squawkAllocationService->createOrUpdateAllocation($callsign, $squawk);
                        }
                    }
                }
            }
        }

        // No squawk found, throw exception.
        throw new SquawkNotAllocatedException('Unable to allocate squawk from available ranges for ' . $callsign);
    }

    /**
     * Searches through a range to find an unallocated squawk
     *
     * @param  Range $range The range to check.
     * @return array|boolean Returns false when unable to find a squawk, else a success array
     */
    private function searchForSquawkInRange(Range $range)
    {
        $squawk = null;
        $found_unallocated_squawk = false;
        $count = 0;
        $unsuitable_squawks = [];

        while (!$found_unallocated_squawk) {
            $squawk = $range->random_squawk;
            if (!$squawk) {
                // Lets not just loop forever
                $squawk = null;
                $found_unallocated_squawk = true;
            }
            $count++;

            // Check to make sure we haven't already tried this squawk
            if (array_search($squawk, $unsuitable_squawks) === false) {
                $unsuitable_squawks[] = $squawk;

                //Check if the squawk is already allocated
                $num_results = Allocation::where('squawk', '=', $squawk)->count();

                // Not already allocated OR duplicates allowed then we're good.
                if ($num_results == 0 || $range->allow_duplicate) {
                    $found_unallocated_squawk = true;
                }
            }

            if ($range->number_of_possibilities < $count) {
                // Lets not just loop forever
                $squawk = null;
                $found_unallocated_squawk = true;
            }
        }

        return $squawk ?: false;
    }
}
