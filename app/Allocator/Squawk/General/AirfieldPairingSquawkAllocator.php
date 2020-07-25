<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\AbstractSquawkAllocator;
use App\Allocator\Squawk\SquawkAssignmentCategories;
use App\Allocator\Squawk\SquawkAllocatorInterface;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkAssignment;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkRange;
use App\Services\NetworkDataService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AirfieldPairingSquawkAllocator extends AbstractSquawkAllocator implements
    SquawkAllocatorInterface
{
    private function getPossibleRangesForFlight(string $origin, string $destination): Collection
    {
        return AirfieldPairingSquawkRange::where('origin', $origin)
            ->where('destination', $destination)
            ->get();
    }

    public function allocate(string $callsign, array $details): ?SquawkAssignmentInterface
    {
        if (!isset($details['origin'])) {
            Log::error(
                'Origin not provided for airfield pairing squawk allocation',
                [$callsign, $details]
            );
            return null;
        }

        if (!isset($details['destination'])) {
            Log::error(
                'Destination not provided for airfield pairing squawk allocation',
                [$callsign, $details]
            );
            return null;
        }

        $assignment = null;
        DB::transaction(
            function () use (&$assignment, $callsign, $details) {
                $this->getPossibleRangesForFlight(
                    $details['origin'],
                    $details['destination']
                )->each(
                    function (AirfieldPairingSquawkRange $range) use (&$assignment, $callsign) {
                        $allSquawks = $range->getAllSquawksInRange();
                        $possibleSquawks = $allSquawks->diff(
                            AirfieldPairingSquawkAssignment::whereIn('code', $allSquawks)
                                ->pluck('code')
                                ->all()
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
        return AirfieldPairingSquawkAssignment::destroy($callsign);
    }

    public function fetch(string $callsign): ?SquawkAssignmentInterface
    {
        return AirfieldPairingSquawkAssignment::find($callsign);
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
                return AirfieldPairingSquawkAssignment::updateOrCreate(
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
