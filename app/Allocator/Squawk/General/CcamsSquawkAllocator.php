<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\AbstractSquawkAllocator;
use App\Allocator\Squawk\SquawkAssignmentCategories;
use App\Allocator\Squawk\SquawkAllocatorInterface;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use App\Services\NetworkDataService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CcamsSquawkAllocator extends AbstractSquawkAllocator implements SquawkAllocatorInterface
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

    public function canAllocateForCategory(string $category): bool
    {
        return $category === SquawkAssignmentCategories::GENERAL;
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
