<?php

namespace App\Services\Stand;

use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use Illuminate\Support\Collection;

class StandStatusService
{
    /**
     * Assignments are preferred to reservations as reservations may be overridden by controllers.
     *
     * Get all assigned stands as well as any active reservations.
     */
    public static function getAirfieldStandStatus(string $airfield): array
    {
        $stands = Stand::with(
            'type',
            'maxAircraftWingspan',
            'maxAircraftLength',
            'assignment',
            'occupier',
            'activeReservations',
            'pairedStands.assignment',
            'pairedStands.occupier',
            'pairedStands.activeReservations',
            'pairedStands.reservationsInNextHour',
            'requests',
            'activeRequests',
            'reservations',
            'reservationsInNextHour'
        )
            ->withCasts(['latitude' => 'decimal:8', 'longitude' => 'decimal:8'])
            ->airfield($airfield)
            ->get();

        $stands->sortBy('identifier', SORT_NATURAL);

        $standStatuses = [];

        foreach ($stands as $stand) {
            $standStatuses[] = self::getStandStatus($stand);
        }

        return $standStatuses;
    }

    public static function getStandStatus(Stand $stand): array
    {
        $standData = [
            'identifier' => $stand->identifier,
            'type' => $stand->type ? $stand->type->key : null,
            'latitude' => $stand->latitude,
            'longitude' => $stand->longitude,
            'airlines' => $stand->airlines->groupBy('icao_code')->map(function (Collection $airlineDestination) {
                return $airlineDestination->filter(function (Airline $airline) {
                    return $airline->pivot->destination;
                })->map(function (Airline $airline) {
                    return $airline->pivot->destination;
                });
            })->toArray(),
            'aerodrome_reference_code' => $stand->aerodrome_reference_code,
            'max_aircraft' => $stand->maxAircraftWingspan && $stand->maxAircraftLength ? [
                'wingspan' => $stand->maxAircraftWingspan->code,
                'length' => $stand->maxAircraftLength->code,
            ] : null,
        ];

        if ($stand->isClosed()) {
            $standData['status'] = 'closed';
        } elseif ($stand->occupier->first()) {
            $standData['status'] = 'occupied';
            $standData['callsign'] = $stand->occupier->first()->callsign;
        } elseif ($stand->assignment) {
            $standData['status'] = 'assigned';
            $standData['callsign'] = $stand->assignment->callsign;
        } elseif (!$stand->activeReservations->isEmpty()) {
            $standData['status'] = 'reserved';
            $standData['callsign'] = $stand->activeReservations->first()->callsign;
        } elseif (!$stand->reservationsInNextHour->isEmpty()) {
            $standData['status'] = 'reserved_soon';
            $standData['reserved_at'] = $stand->reservationsInNextHour->first()->start;
            $standData['callsign'] = $stand->reservationsInNextHour->first()->callsign;
        } elseif (
            !$stand->pairedStands->filter(function (Stand $stand) {
                return $stand->assignment ||
                    !$stand->occupier->isEmpty() ||
                    !$stand->activeReservations->isEmpty();
            })->isEmpty()
        ) {
            $standData['status'] = 'unavailable';
        } elseif (!$stand->activeRequests->isEmpty()) {
            $standData['status'] = 'requested';
            $standData['requested_by'] = $stand->activeRequests->pluck('callsign');
        } else {
            $standData['status'] = 'available';
        }

        return $standData;
    }
}
