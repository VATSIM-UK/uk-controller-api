<?php

namespace App\Listeners\Network;

use App\BaseFunctionalTestCase;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Models\Vatsim\NetworkAircraft;
use App\Models\Vatsim\NetworkAircraftFirEvent;
use Carbon\Carbon;

class RecordFirEntryTest extends BaseFunctionalTestCase
{
    const CALLSIGN = 'EXS16A';
    const IN_FIR_LATITUDE = '53.35889';
    const IN_FIR_LONGITUDE = '-2.27648';
    const OUTSIDE_FIR_LATITUDE = '51.12653';
    const OUTSIDE_FIR_LONGITUDE = '2.08247';
    const FIR_ID = 1;
    const JSON_ENTRY_LATITUDE = 'metadata->entry_latitude';
    const JSON_ENTRY_LONGITUDE = 'metadata->entry_longitude';
    const JSON_EXIT_LATITUDE = 'metadata->exit_latitude';
    const JSON_EXIT_LONGITUDE = 'metadata->exit_longitude';
    const ENTRY_EVENT = 'FIR_ENTRY';
    const EXIT_EVENT = 'FIR_EXIT';

    /**
     * @var RecordFirEntry
     */
    private $listener;

    public function setUp(): void
    {
        parent::setUp();
        $this->listener = $this->app->make(RecordFirEntry::class);
    }

    public function testItRecordsFirExit()
    {
        // The coordinates are out of the FIR
        $aircraft = $this->addEntryEvent(self::CALLSIGN, self::OUTSIDE_FIR_LATITUDE, self::OUTSIDE_FIR_LONGITUDE, self::FIR_ID);

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseHas(
            'network_aircraft_fir_events',
            [
                'callsign' => self::CALLSIGN,
                'flight_information_region_id' => self::FIR_ID,
                'event_type' => self::EXIT_EVENT,
                self::JSON_EXIT_LATITUDE => self::OUTSIDE_FIR_LATITUDE,
                self::JSON_EXIT_LONGITUDE => self::OUTSIDE_FIR_LONGITUDE,
            ]
        );
    }

    public function testItDoesntRecordExitIfStillInFir()
    {
        // The coordinates are in the FIR
        $aircraft = $this->addEntryEvent(self::CALLSIGN, self::IN_FIR_LATITUDE, self::IN_FIR_LONGITUDE, self::FIR_ID);

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseMissing(
            'network_aircraft_fir_events',
            [
                'callsign' => self::CALLSIGN,
                'event_type' => self::EXIT_EVENT,
            ]
        );
    }

    public function testItDoesntRecordExitIfNeverEnteredFir()
    {
        // The coordinates are out of the FIR
        $aircraft = $this->addNetworkAircraft(self::CALLSIGN, self::OUTSIDE_FIR_LATITUDE, self::OUTSIDE_FIR_LONGITUDE);

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseMissing(
            'network_aircraft_fir_events',
            [
                'callsign' => self::CALLSIGN,
                'event_type' => self::EXIT_EVENT,
            ]
        );
    }

    public function testItRecordsFirEntry()
    {
        // The coordinates are in the FIR
        $aircraft = $this->addNetworkAircraft(self::CALLSIGN, self::IN_FIR_LATITUDE, self::IN_FIR_LONGITUDE);

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseHas(
            'network_aircraft_fir_events',
            [
                'callsign' => self::CALLSIGN,
                'event_type' => self::ENTRY_EVENT,
                self::JSON_ENTRY_LATITUDE => self::IN_FIR_LATITUDE,
                self::JSON_ENTRY_LONGITUDE => self::IN_FIR_LONGITUDE,
            ]
        );
    }

    public function testItRecordsRentryIfAfterExit()
    {
        // The coordinates are in the FIR
        $aircraft = $this->addEntryEvent(self::CALLSIGN, self::IN_FIR_LATITUDE, self::IN_FIR_LONGITUDE, self::FIR_ID);
        $event = NetworkAircraftFirEvent::create(
            [
                'callsign' => self::CALLSIGN,
                'flight_information_region_id' => self::FIR_ID,
                'event_type' => self::EXIT_EVENT,
                'metadata' => [],
            ]
        );
        $event->created_at = Carbon::now()->addMinute();
        $event->save();

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseHas(
            'network_aircraft_fir_events',
            [
                'callsign' => self::CALLSIGN,
                'event_type' => self::ENTRY_EVENT,
                self::JSON_ENTRY_LATITUDE => self::IN_FIR_LATITUDE,
                self::JSON_ENTRY_LONGITUDE => self::IN_FIR_LONGITUDE,
            ]
        );
    }

    public function testItDoesntRecordDuplicateEntry()
    {
        // The coordinates are in the FIR
        $aircraft = $this->addEntryEvent(self::CALLSIGN, self::IN_FIR_LATITUDE, self::IN_FIR_LONGITUDE, self::FIR_ID);

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseMissing(
            'network_aircraft_fir_events',
            [
                'callsign' => self::CALLSIGN,
                'event_type' => self::ENTRY_EVENT,
                self::JSON_ENTRY_LATITUDE => self::IN_FIR_LATITUDE,
                self::JSON_ENTRY_LONGITUDE => self::IN_FIR_LONGITUDE,
            ]
        );
    }

    public function testItDoesntRecordEntryIfNotInFir()
    {
        // The coordinates are out of the FIR
        $aircraft = $this->addNetworkAircraft(self::CALLSIGN, self::OUTSIDE_FIR_LATITUDE, self::OUTSIDE_FIR_LONGITUDE);

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseMissing(
            'network_aircraft_fir_events',
            [
                'callsign' => self::CALLSIGN,
                'event_type' => self::ENTRY_EVENT,
            ]
        );
    }

    private function addEntryEvent(
        string $callsign,
        string $latitude,
        string $longitude,
        int $firId
    ): NetworkAircraft {
        $aircraft = $this->addNetworkAircraft($callsign, $latitude, $longitude);
        NetworkAircraftFirEvent::create(
            [
                'callsign' => $callsign,
                'flight_information_region_id' => $firId,
                'event_type' => self::ENTRY_EVENT,
                'metadata' => [],
            ]
        );
        return $aircraft;
    }

    private function addNetworkAircraft(
        string $callsign,
        string $latitude,
        string $longitude
    ): NetworkAircraft {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]
        );
    }
}
