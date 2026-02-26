<?php

namespace App\Support;

use Illuminate\Support\Collection;

class StandReservationPayloadRows
{
    public static function fromPayload(array $payload): Collection
    {
        $defaultStart = $payload['event_start'] ?? $payload['start'] ?? null;
        $defaultEnd = $payload['event_finish'] ?? $payload['end'] ?? null;

        // Backward-compatible flat reservation rows.
        $reservationRows = collect($payload['reservations'] ?? [])
            ->filter(fn (mixed $reservation): bool => is_array($reservation))
            ->map(
                fn (array $reservation): Collection => self::buildRow($reservation, $defaultStart, $defaultEnd)
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
                        self::buildRow($slotReservation, $defaultStart, $defaultEnd, $slotAirfield, $slotStand)
                    );
            });

        return $reservationRows->concat($slotRows)->values();
    }

    private static function buildRow(
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
