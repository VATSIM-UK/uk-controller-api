<?php

namespace App\Allocator\Squawk\General;

use App\Allocator\Squawk\AbstractSquawkAllocator;
use App\Allocator\Squawk\SquawkAssignmentInterface;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkAssignment;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkRange;
use App\Services\NetworkDataService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AirfieldPairingSquawkAllocator extends AbstractSquawkAllocator implements GeneralSquawkAllocatorInterface
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
                            $possibleSquawks->shuffle()
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

    public function assignToCallsign(string $code, string $callsign): ?SquawkAssignmentInterface
    {
        $assignment = null;
        AirfieldPairingSquawkRange::all()->each(
            function (AirfieldPairingSquawkRange $range) use ($code, $callsign, &$assignment) {
                if ($range->squawkInRange($code)) {
                    // Lock the range to prevent a dupe assignment
                    AirfieldPairingSquawkRange::lockForUpdate($range->id)->first();

                    // Assign the code, if we can.
                    if (!AirfieldPairingSquawkAssignment::where('code', $code)->first()) {
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
