<?php

namespace App\Listeners\Network;

use App\Events\NetworkAircraftUpdatedEvent;
use App\Models\FlightInformationRegion\FlightInformationRegion;
use App\Models\Vatsim\NetworkAircraftFirEvent;
use App\Services\FlightInformationRegionService;
use Location\Coordinate;
use Location\Polygon;

class RecordFirEntry
{
    const EVENT_FIR_ENTRY = 'FIR_ENTRY';
    const EVENT_FIR_EXIT = 'FIR_EXIT';

    /**
     * @var Polygon[]
     */
    private $boundaries;

    public function __construct()
    {
        $this->boundaries = FlightInformationRegion::all()
            ->mapWithKeys(
                function (FlightInformationRegion $region) {
                    return [
                        $region->id => FlightInformationRegionService::getBoundaryPolygon(
                            $region->identification_code
                        )
                    ];
                }
            )
            ->toArray();
    }

    public function handle(NetworkAircraftUpdatedEvent $event): bool
    {
        $lastEntry = NetworkAircraftFirEvent::where('callsign', $event->getAircraft()->callsign)
            ->where('event_type', self::EVENT_FIR_ENTRY)
            ->latest('created_at')
            ->first();

        // Handle FIR exit
        if (
            $lastEntry &&
            !$this->hasPreviouslyExitedFir(
                $event->getAircraft()->callsign,
                $lastEntry->flight_information_region_id
            )
        ) {
            $isWithinFirBoundary = isset($this->boundaries[$lastEntry->flight_information_region_id]) &&
                $this->boundaries[$lastEntry->flight_information_region_id]->contains(
                    new Coordinate(
                        $event->getAircraft()->latitude,
                        $event->getAircraft()->longitude
                    )
                );

            if (!$isWithinFirBoundary) {
                NetworkAircraftFirEvent::create(
                    [
                        'callsign' => $event->getAircraft()->callsign,
                        'flight_information_region_id' => $lastEntry->flight_information_region_id,
                        'event_type' => self::EVENT_FIR_EXIT,
                        'metadata' => [
                            'exit_latitude' => $event->getAircraft()->latitude,
                            'exit_longitude' => $event->getAircraft()->longitude,
                        ],
                    ]
                );
            }
        }

        // Handle FIR entry
        foreach ($this->boundaries as $firId => $boundary) {
            // Don't record entering the same FIR unless it has since exited.
            if ($lastEntry && $firId === $lastEntry->flight_information_region_id) {
                $hasExited = NetworkAircraftFirEvent::where(
                    'callsign',
                    $event->getAircraft()->callsign
                )
                    ->where(
                        'flight_information_region_id',
                        $lastEntry->flight_information_region_id
                    )
                    ->where('event_type', self::EVENT_FIR_EXIT)
                    ->where('created_at', '>', $lastEntry->created_at)
                    ->latest('created_at')
                    ->first();

                if (!$hasExited) {
                    continue;
                }
            }


            $isWithinFirBoundary = isset($this->boundaries[$firId]) &&
                $this->boundaries[$firId]->contains(
                    new Coordinate(
                        $event->getAircraft()->latitude,
                        $event->getAircraft()->longitude
                    )
                );

            if ($isWithinFirBoundary) {
                NetworkAircraftFirEvent::create(
                    [
                        'callsign' => $event->getAircraft()->callsign,
                        'flight_information_region_id' => $firId,
                        'event_type' => self::EVENT_FIR_ENTRY,
                        'metadata' => [
                            'entry_latitude' => $event->getAircraft()->latitude,
                            'entry_longitude' => $event->getAircraft()->longitude,
                        ],
                    ]
                );
                break;
            }
        }

        return true;
    }

    private function hasPreviouslyExitedFir(string $callsign, int $firId): bool
    {
        return NetworkAircraftFirEvent::where('callsign', $callsign)
            ->where('flight_information_region_id', $firId)
            ->where('event_type', self::EVENT_FIR_EXIT)
            ->latest('created_at')
            ->get()
            ->isNotEmpty();
    }
}
