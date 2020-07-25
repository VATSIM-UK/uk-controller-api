<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\AbstractSquawkAllocator;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use App\Services\NetworkDataService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CcamsSquawkAllocator extends AbstractSquawkAllocator
    implements GeneralSquawkAllocatorInterface
{
    public function allocate(string $callsign, array $details): ?SquawkAssignmentInterface
    {
        $assignment = null;
        DB::transaction(
            function () use (&$assignment, $callsign) {
                CcamsSquawkRange::all()->shuffle()->each(
                    function (CcamsSquawkRange $range) use (&$assignment, $callsign) {
                        $squawks = $range->getAllSquawksInRange();
                        $possibleSquawks = $squawks->diff(
                            CcamsSquawkAssignment::whereIn('code', $squawks)->pluck('code')->all()
                        );

                        if ($possibleSquawks->isEmpty()) {
                            return true;
                        }

                        $assignment = $this->assignSquawkFromAvailableCodes(
                            $callsign,
                            $possibleSquawks
                        );
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
                        $assignment = $this->assignSquawkFromAvailableCodes(
                            $callsign,
                            new Collection([$code])
                        );
                    }

                    // In any case, if we've found the range with the code, we shouldn't try others.
                    return false;
                }

                return true;
            }
        );

        return $assignment;
    }

    /**
     * @param string $callsign
     * @param Collection $possibleSquawks
     * @return SquawkAssignmentInterface|null
     */
    private function assignSquawkFromAvailableCodes(
        string $callsign,
        Collection $possibleSquawks
    ): ?SquawkAssignmentInterface {
        NetworkDataService::firstOrCreateNetworkAircraft($callsign);
        return $this->assignSquawk(
            function (string $code) use ($callsign) {
                return CcamsSquawkAssignment::updateOrCreate(
                    [
                        'callsign' => $callsign,
                        'code' => $code,
                    ]
                );
            },
            $possibleSquawks
        );
    }
}
