<?php

namespace App\Services\Stand;

use Illuminate\Support\Collection;

class StandReservationPayloadRowsBuilder
{
    public function fromPayload(array $payload): Collection
    {
        $defaultStart = $payload['event_start'] ?? $payload['start'] ?? null;
        $defaultEnd = $payload['event_finish'] ?? $payload['end'] ?? null;

        return $this->rowsFromReservations($payload['reservations'] ?? [], $defaultStart, $defaultEnd)
            ->concat($this->rowsFromStandSlots($payload['stand_slots'] ?? [], $defaultStart, $defaultEnd))
            ->values();
    }

    private function rowsFromStandSlots(array $standSlots, ?string $defaultStart, ?string $defaultEnd): Collection
    {
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
        return collect([
            'airport' => $reservation['airport'] ?? $fallbackAirport,
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
