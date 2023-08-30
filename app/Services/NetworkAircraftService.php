<?php

namespace App\Services;

use App\Events\NetworkDataUpdatedEvent;
use App\Jobs\Network\AircraftDisconnected;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Location\Coordinate;
use Location\Distance\Haversine;

class NetworkAircraftService
{
    const MAX_PROCESSING_DISTANCE = 700;

    /**
     * @var Coordinate[]
     */
    private Collection $measuringPoints;
    private NetworkDataService $dataService;
    private Collection $allAircraftBeforeUpdate;

    private readonly AircraftService $aircraftService;

    public function __construct(
        NetworkDataService $dataService,
        AircraftService $aircraftService,
        Collection $measuringPoints
    ) {
        $this->measuringPoints = $measuringPoints;
        $this->aircraftService = $aircraftService;
        $this->dataService = $dataService;
    }

    public function updateNetworkData(): void
    {
        $this->allAircraftBeforeUpdate = NetworkAircraft::all()->mapWithKeys(function (NetworkAircraft $aircraft)
        {
            return [$aircraft->callsign => $aircraft];
        });

        $concernedPilots = $this->formatPilotData($this->dataService->getNetworkAircraftData());
        $this->processPilots($concernedPilots);
        $this->handleTimeouts();
        event(new NetworkDataUpdatedEvent());
    }

    private function formatPilotData(Collection $pilots): Collection
    {
        return $this->mapPilotData($this->filterPilotData($pilots));
    }

    private function mapPilotData(Collection $pilotData): Collection
    {
        return $pilotData->map(function (array $pilot)
        {
            return $this->formatPilot($pilot);
        });
    }

    private function filterPilotData(Collection $pilotData): Collection
    {
        return $pilotData->filter(function (array $pilot)
        {
            return $this->shouldProcessPilot($pilot) &&
                $this->pilotValid($pilot);
        });
    }

    /**
     * Loop through each client in the clients array from the network data
     */
    private function processPilots(Collection $pilots): void
    {
        NetworkAircraft::upsert(
            $pilots->toArray(),
            ['callsign']
        );
    }

    private function shouldProcessPilot(array $pilot): bool
    {
        return $this->measuringPoints->contains(function (Coordinate $coordinate) use ($pilot)
        {
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
        $shortAircraftCode = $this->getFlightplanDataElement($pilot, 'aircraft_short');

        return [
            'callsign' => $pilot['callsign'],
            'cid' => $pilot['cid'],
            'latitude' => $pilot['latitude'],
            'longitude' => $pilot['longitude'],
            'altitude' => $pilot['altitude'],
            'groundspeed' => $pilot['groundspeed'],
            'transponder' => $pilot['transponder'],
            'planned_aircraft' => $this->getFlightplanDataElement($pilot, 'aircraft'),
            'planned_aircraft_short' => $shortAircraftCode,
            'planned_depairport' => $this->getFlightplanDataElement($pilot, 'departure'),
            'planned_destairport' => $this->getFlightplanDataElement($pilot, 'arrival'),
            'planned_altitude' => $this->getFlightplanDataElement($pilot, 'altitude'),
            'planned_flighttype' => $this->getFlightplanDataElement($pilot, 'flight_rules'),
            'planned_route' => $this->getFlightplanDataElement($pilot, 'route'),
            'remarks' => $this->getFlightplanDataElement($pilot, 'remarks'),
            'transponder_last_updated_at' => $this->getTransponderUpdatedAtTime($pilot),
            'aircraft_id' => $shortAircraftCode 
                ? $this->aircraftService->getAircraftIdFromCode($shortAircraftCode)
                : null,
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
     * If any aircraft has passed the timeout window, trigger the timeout event to have it removed.
     */
    private function handleTimeouts(): void
    {
        NetworkAircraft::timedOut()
            ->get()
            ->each(
                function (NetworkAircraft $aircraft): void
                {
                    AircraftDisconnected::dispatchSync($aircraft);
                }
            );
    }

    public static function createOrUpdateNetworkAircraft(
        string $callsign,
        array $details = []
    ): NetworkAircraft {
        NetworkAircraft::upsert(
            array_merge(
                [
                    'callsign' => $callsign,
                ],
                $details
            ),
            ['callsign'],
            array_merge(['callsign'], array_keys($details)),
        );
        return NetworkAircraft::find($callsign);
    }

    /**
     * This method should not be used in a transaction, as we can't guarantee
     * that the row won't be created by someone else.
     */
    public static function createPlaceholderAircraft(string $callsign): void
    {
        if (NetworkAircraft::where('callsign', $callsign)->exists()) {
            return;
        }

        NetworkAircraft::upsert(
            [
                'callsign' => $callsign,
            ],
            ['callsign'],
            ['callsign'],
        );
    }

    private function pilotValid(array $pilot): bool
    {
        return preg_match('/^[0-7]{4}$/', $pilot['transponder']);
    }
}
