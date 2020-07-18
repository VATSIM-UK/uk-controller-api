<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Models\Vatsim\NetworkAircraftFirEvent;
use Carbon\Carbon;

class ReclaimSquawksAfterFirExitTest extends BaseFunctionalTestCase
{
    const ARTISAN_COMMAND = 'squawks:reclaim';
    
    /**
     * @var NetworkAircraft
     */
    private $aircraft;

    public function setUp(): void
    {
        parent::setUp();
        $this->aircraft = NetworkAircraft::create(
            [
                'callsign' => 'EXS16A',
                'latitude' => '53.35889',
                'longitude' => '-2.27648',
            ]
        );

        CcamsSquawkAssignment::create(
            [
                'callsign' => 'EXS16A',
                'code' => '1234'
            ]
        );
        Carbon::setTestNow(Carbon::now());
    }

    public function testItReclaimsSquawkIfAircraftExitedFir()
    {
        $entryEvent = NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_ENTRY',
                'flight_information_region_id' => 1,
                'metadata' => []
            ]
        );
        $entryEvent->created_at = Carbon::now()->subHours(3);
        $entryEvent->save();

        $exitEvent = NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_EXIT',
                'flight_information_region_id' => 1,
                'metadata' => []
            ]
        );

        $exitEvent->created_at = Carbon::now()->subHours(1)->subSecond();
        $exitEvent->save();
        $this->artisan(self::ARTISAN_COMMAND);
        $this->assertDatabaseMissing(
            'ccams_squawk_assignments',
            [
                'callsign' => 'EXS16A'
            ]
        );
    }

    public function testItDoesntReclaimAssignmentIfNotOusideFirForLongEnough()
    {
        $entryEvent = NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_ENTRY',
                'flight_information_region_id' => 1,
                'metadata' => []
            ]
        );
        $entryEvent->created_at = Carbon::now()->subHours(3);
        $entryEvent->save();

        $exitEvent = NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_EXIT',
                'flight_information_region_id' => 1,
                'metadata' => []
            ]
        );

        $exitEvent->created_at = Carbon::now()->subHours(1)->addSecond();
        $exitEvent->save();
        $this->artisan(self::ARTISAN_COMMAND);
        $this->assertDatabaseHas(
            'ccams_squawk_assignments',
            [
                'callsign' => 'EXS16A'
            ]
        );
    }

    public function testItDoesntReclaimAssignmentIfEnteredAnotherFirSince()
    {
        $exitEvent = NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_EXIT',
                'flight_information_region_id' => 1,
                'metadata' => []
            ]
        );

        $exitEvent->created_at = Carbon::now()->subHours(6);
        $exitEvent->save();

        $entryEvent = NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_ENTRY',
                'flight_information_region_id' => 2,
                'metadata' => []
            ]
        );
        $entryEvent->created_at = Carbon::now()->subHours(5);
        $entryEvent->save();

        $entryEvent->save();
        $this->artisan(self::ARTISAN_COMMAND);
        $this->assertDatabaseHas(
            'ccams_squawk_assignments',
            [
                'callsign' => 'EXS16A'
            ]
        );
    }

    public function testItDoesntReclaimAssignmentIfHasNotExitedFir()
    {
        $entryEvent = NetworkAircraftFirEvent::create(
            [
                'callsign' => 'EXS16A',
                'event_type' => 'FIR_ENTRY',
                'flight_information_region_id' => 2,
                'metadata' => []
            ]
        );
        $entryEvent->created_at = Carbon::now()->subHours(5);
        $entryEvent->save();

        $entryEvent->save();
        $this->artisan(self::ARTISAN_COMMAND);
        $this->assertDatabaseHas(
            'ccams_squawk_assignments',
            [
                'callsign' => 'EXS16A'
            ]
        );
    }
}
