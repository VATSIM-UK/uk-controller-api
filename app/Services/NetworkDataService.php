<?php

namespace App\Services;

use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Location\Coordinate;
use Location\Distance\Haversine;

class NetworkDataService
{
    const NETWORK_DATA_URL = "https://data.vatsim.net/v3/vatsim-data.json";
    const MAX_PROCESSING_DISTANCE = 700;
    private Collection $allAircraftBeforeUpdate;

    /**
     * @var Coordinate[]
     */
    private Collection $measuringPoints;

    public function __construct(Collection $measuringPoints)
    {
        $this->measuringPoints = $measuringPoints;
    }

    public function updateNetworkData(): void
    {
        $this->allAircraftBeforeUpdate = NetworkAircraft::all()->mapWithKeys(function (NetworkAircraft $aircraft) {
            return [$aircraft->callsign => $aircraft];
        });

        // Download the network data and check that it was successful
        $networkResponse = null;
        try {
            $networkResponse = Http::get(self::NETWORK_DATA_URL);
        } catch (Exception $exception) {
            Log::warning('Failed to download network data, exception was ' . $exception->getMessage());
            return;
        }

        if (!$networkResponse->successful()) {
            Log::warning('Failed to download network data, response was ' . $networkResponse->status());
            return;
        }

        // Process clients
        $concernedPilots = $this->formatPilotData($networkResponse);
        $this->processPilots($concernedPilots);
        $this->handleTimeouts();
        $this->triggerUpdatedEvents($concernedPilots);
    }

    private function triggerUpdatedEvents(Collection $concernedPilots)
    {
        NetworkAircraft::whereIn('callsign', $concernedPilots->pluck('callsign'))
            ->get()
            ->each(function (NetworkAircraft $aircraft) {
                event(new NetworkAircraftUpdatedEvent($aircraft));
            });
    }

    private function formatPilotData(Response $response): Collection
    {
        return $this->mapPilotData($this->filterPilotData(new Collection($response->json('pilots', []))));
    }

    private function mapPilotData(Collection $pilotData): Collection
    {
        $pilotData->filter(function (array $pilot) {
            return $this->formatPilot($pilot);
        });
    }

    private function filterPilotData(Collection $pilotData): Collection
    {
        return $pilotData->filter(function (array $pilot) {
            return $this->shouldProcessPilot($pilot);
        });
    }

    /**
     * Loop through each client in the clients array from the network data
     */
    private function processPilots(Collection $pilots): void
    {
        $filteredPilots = $pilots->filter(
            function (array $pilot) {
                return $this->shouldProcessPilot($pilot);
            }
        )
            ->map(
                function (array $pilot) {
                    return $this->formatPilot($pilot);
                }
            );

        NetworkAircraft::upsert(
            $filteredPilots->toArray(),
            ['callsign']
        );
    }

    private function shouldProcessPilot(array $pilot): bool
    {
        return $this->measuringPoints->contains(function (Coordinate $coordinate) use ($pilot) {
            return LocationService::metersToNauticalMiles(
                $coordinate->getDistance(new Coordinate($pilot['latitude'], $pilot['longitude']), new Haversine())
            ) < self::MAX_PROCESSING_DISTANCE;
        });
    }

    /**
     * Formats a pilot from the V3 datafeed.
     */
    private function formatPilot(array $pilot): array
    {
        return [
            'callsign' => $pilot['callsign'],
            'latitude' => $pilot['latitude'],
            'longitude' => $pilot['longitude'],
            'altitude' => $pilot['altitude'],
            'groundspeed' => $pilot['groundspeed'],
            'transponder' => Str::padLeft($pilot['transponder'], '0', 4),
            'planned_aircraft' => $this->getFlightplanDataElement($pilot, 'aircraft'),
            'planned_depairport' => $this->getFlightplanDataElement($pilot, 'departure'),
            'planned_destairport' => $this->getFlightplanDataElement($pilot, 'arrival'),
            'planned_altitude' => $this->getFlightplanDataElement($pilot, 'altitude'),
            'planned_flighttype' => $this->getFlightplanDataElement($pilot, 'flight_rules'),
            'planned_route' => $this->getFlightplanDataElement($pilot, 'route'),
            'transponder_last_updated_at' => $this->getTransponderUpdatedAtTime($pilot),
        ];
    }

    /**
     * Set the transponder updated at time based on whether the transponder has been changed
     * since the last time we polled.
     */
    private function getTransponderUpdatedAtTime(array $pilot): Carbon
    {
        return $this->allAircraftBeforeUpdate->has($pilot['callsign']) &&
        $this->allAircraftBeforeUpdate->get($pilot['callsign'])->transponder === $pilot['transponder']
            ? $this->allAircraftBeforeUpdate->get($pilot['callsign'])->transponder_last_updated_at
            : Carbon::now();
    }

    /**
     * Returns a data element from a V3 flightplan if one exists.
     */
    private function getFlightplanDataElement(array $pilot, string $element): ?string
    {
        return $pilot['flight_plan'][$element] ?? null;
    }

    /**
     * If any aircraft has passed the timeout window, remove it from the list.
     *
     * NOTE: Events should always fire before final deletion because the listeners
     * will use the aircraft data to mark things such as squawk assignments as deleted
     * and send further events. As a last resort calling delete here will delete any
     * foreign key references left over.
     */
    private function handleTimeouts(): void
    {
        NetworkAircraft::where('updated_at', '<', Carbon::now()->subMinutes(20))
            ->get()
            ->each(
                function (NetworkAircraft $aircraft) {
                    $aircraft->getConnection()->transaction(
                        function () use ($aircraft) {
                            event(new NetworkAircraftDisconnectedEvent($aircraft));
                            $aircraft->delete();
                        }
                    );
                }
            );
    }

    public static function createOrUpdateNetworkAircraft(
        string $callsign,
        array $details = []
    ): NetworkAircraft {
        try {
            $aircraft = NetworkAircraft::updateOrCreate(
                ['callsign' => $callsign],
                array_merge(
                    ['callsign' => $callsign],
                    $details
                )
            );
            $aircraft->touch();
        } catch (QueryException $queryException) {
            if ($queryException->errorInfo[1] !== 1062) {
                throw $queryException;
            }
            $aircraft = NetworkAircraft::find($callsign);
        }

        return $aircraft;
    }

    public static function firstOrCreateNetworkAircraft(
        string $callsign,
        array $details = []
    ): NetworkAircraft {
        try {
            $aircraft = NetworkAircraft::firstOrCreate(
                ['callsign' => $callsign],
                array_merge(
                    ['callsign' => $callsign],
                    $details
                )
            );
        } catch (QueryException $queryException) {
            if ($queryException->errorInfo[1] !== 1062) {
                throw $queryException;
            }
            $aircraft = NetworkAircraft::find($callsign);
        }

        return $aircraft;
    }
}
