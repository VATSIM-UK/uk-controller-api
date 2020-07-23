<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\SquawkAssignmentCategories;
use App\Allocator\Squawk\SquawkAllocatorInterface;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use App\Services\NetworkDataService;
use Illuminate\Support\Facades\DB;

class CcamsSquawkAllocator implements SquawkAllocatorInterface, GeneralSquawkAllocatorInterface
{
    public function allocate(string $callsign, array $details): ?SquawkAssignmentInterface
    {
        $assignment = null;
        DB::transaction(
            function () use (&$assignment, $callsign) {
                CcamsSquawkRange::all()->shuffle()->each(
                    function (CcamsSquawkRange $range) use (&$assignment, $callsign) {
                        // Lock the range so duplicate squawk allocations cannot happen
                        CcamsSquawkRange::lockForUpdate()->find($range->id);

                        $squawks = $range->getAllSquawksInRange();
                        $possibleSquawks = $squawks->diff(
                            CcamsSquawkAssignment::whereIn('code', $squawks)->pluck('code')->all()
                        );

                        if ($possibleSquawks->isEmpty()) {
                            return true;
                        }

                        $assignment = $this->assignSquawk($possibleSquawks->first(), $callsign);
                        return false;
                    }
                );
            }
        );

        return $assignment;
    }

    public function delete(string $callsign): bool
    {
        return CcamsSquawkAssignment::destroy($callsign);
    }

    public function fetch(string $callsign): ?SquawkAssignmentInterface
    {
        return CcamsSquawkAssignment::find($callsign);
    }

    public function canAllocateForCategory(string $category): bool
    {
        return $category === SquawkAssignmentCategories::GENERAL;
    }

    public function assignToCallsign(string $code, string $callsign): ?SquawkAssignmentInterface
    {
        $assignment = null;
        CcamsSquawkRange::all()->each(
            function (CcamsSquawkRange $range) use ($code, $callsign, &$assignment) {
                if ($range->squawkInRange($code)) {
                    // Lock the range to prevent a dupe assignment
                    CcamsSquawkRange::lockForUpdate($range->id)->first();

                    // Assign the code, if we can.
                    if (!CcamsSquawkAssignment::where('code', $code)->first()) {
                        $assignment = $this->assignSquawk($code, $callsign);
                    }

                    // In any case, if we've found the range with the code, we shouldn't try others.
                    return false;
                }

                return true;
            }
        );

        return $assignment;
    }

    private function assignSquawk(string $squawk, string $callsign): CcamsSquawkAssignment
    {
        NetworkDataService::firstOrCreateNetworkAircraft($callsign);
        return CcamsSquawkAssignment::create(
            [
                'callsign' => $callsign,
                'code' => $squawk
            ]
        );
    }
}
