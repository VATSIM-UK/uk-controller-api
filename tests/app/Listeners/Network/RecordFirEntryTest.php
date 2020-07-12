<?php

namespace App\Listeners\Network;

use App\BaseFunctionalTestCase;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Models\Vatsim\NetworkAircraft;
use App\Models\Vatsim\NetworkAircraftFirEvent;

class RecordFirEntryTest extends BaseFunctionalTestCase
{

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
        $aircraft = NetworkAircraft::create(
            [
                'callsign' => 'EXS16A',
                'latitude' => '51.12653',
                'longitude' => '2.08247',
            ]
        );

        NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'flight_information_region_id' => 1,
                'event_type' => 'FIR_ENTRY',
                'metadata' => [],
            ]
        );

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseHas(
            'network_aircraft_fir_events',
            [
                'callsign' => 'EXS16A',
                'flight_information_region_id' => 1,
                'event_type' => 'FIR_EXIT',
                'metadata->exit_latitude' => '51.12653',
                'metadata->exit_longitude' => '2.08247',
            ]
        );
    }

    public function testItDoesntRecordExitIfStillInFir()
    {
        // The coordinates are out of the FIR
        $aircraft = NetworkAircraft::create(
            [
                'callsign' => 'EXS16A',
                'latitude' => '53.35889',
                'longitude' => '-2.27648',
            ]
        );

        NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'flight_information_region_id' => 1,
                'event_type' => 'FIR_ENTRY',
                'metadata' => [],
            ]
        );

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseMissing(
            'network_aircraft_fir_events',
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_EXIT',
            ]
        );
    }

    public function testItDoesntRecordExitIfNeverEnteredFir()
    {
        // The coordinates are out of the FIR
        $aircraft = NetworkAircraft::create(
            [
                'callsign' => 'EXS16A',
                'latitude' => '51.12653',
                'longitude' => '2.08247',
            ]
        );

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseMissing(
            'network_aircraft_fir_events',
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_EXIT',
            ]
        );
    }

    public function testItRecordsFirEntry()
    {
        // The coordinates are out of the FIR
        $aircraft = NetworkAircraft::create(
            [
                'callsign' => 'EXS16A',
                'latitude' => '53.35889',
                'longitude' => '-2.27648',
            ]
        );

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseHas(
            'network_aircraft_fir_events',
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_ENTRY',
                'metadata->entry_latitude' => '53.35889',
                'metadata->entry_longitude' => '-2.27648',
            ]
        );
    }

    public function testItRecordsRentryIfAfterExit()
    {
        // The coordinates are out of the FIR
        $aircraft = NetworkAircraft::create(
            [
                'callsign' => 'EXS16A',
                'latitude' => '53.35889',
                'longitude' => '-2.27648',
            ]
        );

        NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'flight_information_region_id' => 1,
                'event_type' => 'FIR_ENTRY',
                'metadata' => [],
            ]
        );

        NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'flight_information_region_id' => 1,
                'event_type' => 'FIR_EXIT',
                'metadata' => [],
            ]
        );

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseMissing(
            'network_aircraft_fir_events',
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_ENTRY',
                'metadata->entry_latitude' => '53.35889',
                'metadata->entry_longitude' => '-2.27648',
            ]
        );
    }

    public function testItDoesntRecordDuplicateEntry()
    {
        // The coordinates are out of the FIR
        $aircraft = NetworkAircraft::create(
            [
                'callsign' => 'EXS16A',
                'latitude' => '53.35889',
                'longitude' => '-2.27648',
            ]
        );

        NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'flight_information_region_id' => 1,
                'event_type' => 'FIR_ENTRY',
                'metadata' => [],
            ]
        );

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseMissing(
            'network_aircraft_fir_events',
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_ENTRY',
                'metadata->entry_latitude' => '53.35889',
                'metadata->entry_longitude' => '-2.27648',
            ]
        );
    }

    public function testItDoesntRecordEntryIfNotInFir()
    {
        // The coordinates are out of the FIR
        $aircraft = NetworkAircraft::create(
            [
                'callsign' => 'EXS16A',
                'latitude' => '51.12653',
                'longitude' => '2.08247',
            ]
        );

        $this->listener->handle(new NetworkAircraftUpdatedEvent($aircraft));
        $this->assertDatabaseMissing(
            'network_aircraft_fir_events',
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_ENTRY',
            ]
        );
    }
}
