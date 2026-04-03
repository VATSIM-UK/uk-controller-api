<?php

namespace App\Console\Commands;

use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\Stand\StandAssignmentsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SyncStandReservationAssignments extends Command
{
    private const MANAGED_ASSIGNMENTS_CACHE_KEY = 'stand_reservations:managed_assignments';

    protected $signature = 'stand-reservations:sync-assignments';

    protected $description = 'Sync active reservation stand assignments';

    public function handle(StandAssignmentsService $assignmentsService): int
    {
        // Active reservations represent the currently valid slot windows.
        // Process reservations in chronological order so earlier slots win when overlaps exist.
        $activeReservations = StandReservation::query()->active()->orderBy('start')->get();

        $currentManagedAssignments = [];
        $synchronisedAssignments = 0;

        foreach ($activeReservations as $reservation) {
            $aircraft = $this->matchingAircraftForReservation($reservation);
            if ($aircraft === null) {
                continue;
            }

            // Track assignments created/managed by reservation slots so we can lift them later.
            $currentManagedAssignments[$aircraft->callsign] = $reservation->stand_id;

            $existing = $assignmentsService->assignmentForCallsign($aircraft->callsign);
            if ($existing && $existing->stand_id === $reservation->stand_id) {
                continue;
            }

            // Force the requested stand for this active slot before general auto-allocation can place it elsewhere.
            $assignmentsService->createStandAssignment($aircraft->callsign, $reservation->stand_id, 'Reservation');
            $synchronisedAssignments++;
        }

        /** @var array<string,int> $previousManagedAssignments */
        $previousManagedAssignments = Cache::get(self::MANAGED_ASSIGNMENTS_CACHE_KEY, []);

        // Lift reservation-managed assignments once the matching slot is no longer active.
        foreach ($previousManagedAssignments as $callsign => $standId) {
            if (array_key_exists($callsign, $currentManagedAssignments)) {
                continue;
            }

            $existingAssignment = StandAssignment::find($callsign);
            if ($existingAssignment && $existingAssignment->stand_id === $standId) {
                $assignmentsService->deleteStandAssignment($existingAssignment);
            }
        }

        // Persist only reservation-managed assignments so later runs can safely remove expired ones.
        Cache::forever(self::MANAGED_ASSIGNMENTS_CACHE_KEY, $currentManagedAssignments);

        $this->info(sprintf('Synchronised %d reservation assignment(s).', $synchronisedAssignments));

        return 0;
    }

    // Match by CID only.
    private function matchingAircraftForReservation(StandReservation $reservation): ?NetworkAircraft
    {
        if ($reservation->cid === null) {
            return null;
        }

        return NetworkAircraft::query()
            ->notTimedOut()
            ->where('cid', $reservation->cid)
            ->first();
    }
}
