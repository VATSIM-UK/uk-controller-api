<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\AbstractSquawkAllocator;
use App\Allocator\Squawk\SquawkAssignmentCategories;
use App\Allocator\Squawk\SquawkAllocatorInterface;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Models\Squawk\Orcam\OrcamSquawkAssignment;
use App\Models\Squawk\Orcam\OrcamSquawkRange;
use App\Services\NetworkDataService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection as BaseCollection;

class OrcamSquawkAllocator extends AbstractSquawkAllocator
    implements SquawkAllocatorInterface, GeneralSquawkAllocatorInterface
{
    /**
     * Generate rules to match flights by their origin in order
     * of relevance.
     *
     * @param string $origin
     * @return Collection
     */
    private function getPossibleRangesForFlight(string $origin): Collection
    {
        $originStrings = [
            substr($origin, 0, 1),
            substr($origin, 0, 2),
            substr($origin, 0, 3),
            $origin
        ];

        return OrcamSquawkRange::whereIn('origin', $originStrings)
            ->orderByRaw('LENGTH(origin) DESC')
            ->get();
    }

    public function allocate(string $callsign, array $details): ?SquawkAssignmentInterface
    {
        if (!isset($details['origin'])) {
            Log::error('Origin not provided for ORCAM squawk allocation', [$callsign, $details]);
            return null;
        }

        $assignment = null;
        DB::transaction(
            function () use (&$assignment, $callsign, $details) {
                $this->getPossibleRangesForFlight($details['origin'])->each(
                    function (OrcamSquawkRange $range) use (&$assignment, $callsign) {
                        $allSquawks = $range->getAllSquawksInRange();
                        $possibleSquawks = $allSquawks->diff(
                            OrcamSquawkAssignment::whereIn('code', $allSquawks)
                                ->pluck('code')->all()
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
        return OrcamSquawkAssignment::destroy($callsign);
    }

    public function fetch(string $callsign): ?SquawkAssignmentInterface
    {
        return OrcamSquawkAssignment::find($callsign);
    }

    public function canAllocateForCategory(string $category): bool
    {
        return $category === SquawkAssignmentCategories::GENERAL;
    }

    public function assignToCallsign(string $code, string $callsign): ?SquawkAssignmentInterface
    {
        $assignment = null;
        OrcamSquawkRange::all()->each(
            function (OrcamSquawkRange $range) use ($code, $callsign, &$assignment) {
                if ($range->squawkInRange($code)) {
                    // Lock the range to prevent a dupe assignment
                    OrcamSquawkRange::lockForUpdate($range->id)->first();

                    // Assign the code, if we can.
                    if (!OrcamSquawkAssignment::where('code', $code)->first()) {
                        $assignment = $this->assignSquawkFromAvailableCodes(
                            $callsign,
                            new BaseCollection([$code])
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
     * @param BaseCollection $possibleSquawks
     * @return SquawkAssignmentInterface|null
     */
    private function assignSquawkFromAvailableCodes(
        string $callsign,
        BaseCollection $possibleSquawks
    ): ?SquawkAssignmentInterface {
        NetworkDataService::firstOrCreateNetworkAircraft($callsign);
        return $this->assignSquawk(
            function (string $code) use ($callsign) {
                return OrcamSquawkAssignment::updateOrCreate(
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
