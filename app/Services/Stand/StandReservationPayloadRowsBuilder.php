<?php

namespace App\Services\Stand;

use Illuminate\Support\Collection;

class StandReservationPayloadRowsBuilder
{
    public function fromPayload(array $payload): Collection
    {
        $defaultStart = $payload['event_start'] ?? null;
        $defaultEnd = $payload['event_finish'] ?? null;

        return $this->rowsFromReservations($payload['reservations'] ?? [], $defaultStart, $defaultEnd)
            ->concat($this->rowsFromStandSlots($payload['stand_slots'] ?? [], $defaultStart, $defaultEnd))
            ->values();
    }

    private function rowsFromStandSlots(array $standSlots, ?string $defaultStart, ?string $defaultEnd): Collection
    {
        // Expand stand_slots into concrete reservation rows using slot-level defaults.
        return collect($standSlots)
            ->filter(fn (mixed $standSlot): bool => is_array($standSlot))
            ->flatMap(function (array $standSlot) use ($defaultStart, $defaultEnd): Collection {
                return $this->rowsFromReservations(
                    reservations: $standSlot['slot_reservations'] ?? [],
                    defaultStart: $defaultStart,
                    defaultEnd: $defaultEnd,
                    fallbackAirport: $standSlot['airport'] ?? null,
                    fallbackStand: $standSlot['stand'] ?? null,
                );
            });
    }

    // Normalise every reservation to the importer-friendly associative row structure.
    private function rowsFromReservations(
        array $reservations,
        ?string $defaultStart,
        ?string $defaultEnd,
        ?string $fallbackAirport = null,
        ?string $fallbackStand = null
    ): Collection {
        return collect($reservations)
            ->filter(fn (mixed $reservation): bool => is_array($reservation))
            ->map(
                fn (array $reservation): Collection =>
                    $this->buildReservationRow($reservation, $defaultStart, $defaultEnd, $fallbackAirport, $fallbackStand)
            );
    }

    private function buildReservationRow(
        array $reservation,
        ?string $defaultStart,
        ?string $defaultEnd,
        ?string $fallbackAirport = null,
        ?string $fallbackStand = null
    ): Collection {
        // Output matches the associative format consumed by StandReservationsImport.
        return collect([
            'airport' => $reservation['airport'] ?? $fallbackAirport,
            'stand' => $reservation['stand'] ?? $fallbackStand,
            'callsign' => $reservation['callsign'] ?? null,
            'cid' => $reservation['cid'] ?? null,
            'start' => $reservation['slotstart'] ?? $defaultStart,
            'end' => $reservation['slotend'] ?? $defaultEnd,
        ]);
    }
}
