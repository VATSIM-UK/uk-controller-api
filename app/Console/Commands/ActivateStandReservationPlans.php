<?php

namespace App\Console\Commands;

use App\Imports\Stand\StandReservationsImport;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandReservation;
use App\Models\Stand\StandReservationPlan;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\Stand\StandAssignmentsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ActivateStandReservationPlans extends Command
{
    private const MANAGED_ASSIGNMENTS_CACHE_KEY = 'stand_reservations:managed_assignments';

    protected $signature = 'stand-reservations:activate-plans';

    protected $description = 'Import due stand reservation plans and sync active reservation stand assignments';

    public function handle(StandReservationsImport $importer, StandAssignmentsService $assignmentsService): int
    {
        $plans = StandReservationPlan::query()
            ->where('status', 'approved')
            ->whereNull('imported_reservations')
            ->orderBy('approved_at')
            ->get();

        $activated = 0;

        foreach ($plans as $plan) {
            // Only activate plans once their event window begins.
            $payload = $plan->payload ?? [];
            $eventStart = $payload['event_start'] ?? $payload['start'] ?? null;

            if ($eventStart === null || Carbon::parse($eventStart)->isFuture()) {
                continue;
            }

            $createdReservations = $importer->importReservations($this->rowsFromPayload($payload));

            $plan->update([
                'imported_reservations' => $createdReservations,
            ]);

            $activated++;
        }

        $synchronisedAssignments = $this->syncReservationAssignments($assignmentsService);

        $this->info(sprintf(
            'Activated %d stand reservation plan(s); synchronised %d reservation assignment(s).',
            $activated,
            $synchronisedAssignments
        ));

        return 0;
    }

    private function syncReservationAssignments(StandAssignmentsService $assignmentsService): int
    {
        // Active reservations represent the currently valid slot windows.
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

        Cache::forever(self::MANAGED_ASSIGNMENTS_CACHE_KEY, $currentManagedAssignments);

        return $synchronisedAssignments;
    }

    private function matchingAircraftForReservation(StandReservation $reservation): ?NetworkAircraft
    {
        $query = NetworkAircraft::query()->notTimedOut()->where(function ($query) use ($reservation) {
            $query->where('callsign', $reservation->callsign);

            if ($reservation->cid !== null) {
                $query->orWhere('cid', $reservation->cid);
            }
        });

        if ($reservation->origin !== null) {
            $query->where('planned_depairport', $reservation->origin);
        }

        if ($reservation->destination !== null) {
            $query->where('planned_destairport', $reservation->destination);
        }

        return $query->first();
    }

    private function rowsFromPayload(array $payload): Collection
    {
        $defaultStart = $payload['event_start'] ?? $payload['start'] ?? null;
        $defaultEnd = $payload['event_finish'] ?? $payload['end'] ?? null;

        // Backward-compatible flat reservation rows.
        $reservationRows = collect($payload['reservations'] ?? [])
            ->filter(fn (mixed $reservation): bool => is_array($reservation))
            ->map(
                fn (array $reservation): Collection => $this->buildReservationRow($reservation, $defaultStart, $defaultEnd)
            );

        // Preferred stand-slot rows where each stand contains one or more timed slot reservations.
        $slotRows = collect($payload['stand_slots'] ?? [])
            ->filter(fn (mixed $standSlot): bool => is_array($standSlot))
            ->flatMap(function (array $standSlot) use ($defaultStart, $defaultEnd) {
                $slotAirfield = $standSlot['airfield'] ?? $standSlot['airport'] ?? null;
                $slotStand = $standSlot['stand'] ?? null;

                return collect($standSlot['slot_reservations'] ?? [])
                    ->filter(fn (mixed $slotReservation): bool => is_array($slotReservation))
                    ->map(
                        fn (array $slotReservation): Collection =>
                            $this->buildReservationRow($slotReservation, $defaultStart, $defaultEnd, $slotAirfield, $slotStand)
                    );
            });

        return $reservationRows->concat($slotRows)->values();
    }

    private function buildReservationRow(
        array $reservation,
        ?string $defaultStart,
        ?string $defaultEnd,
        ?string $fallbackAirfield = null,
        ?string $fallbackStand = null
    ): Collection {
        return collect([
            'airfield' => $reservation['airfield'] ?? $reservation['airport'] ?? $fallbackAirfield,
            'stand' => $reservation['stand'] ?? $fallbackStand,
            'callsign' => $reservation['callsign'] ?? null,
            'cid' => $reservation['cid'] ?? null,
            'origin' => $reservation['origin'] ?? null,
            'destination' => $reservation['destination'] ?? null,
            'start' => $reservation['start'] ?? $defaultStart,
            'end' => $reservation['end'] ?? $defaultEnd,
        ]);
    }
}
