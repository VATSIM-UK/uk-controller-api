<?php

namespace App\Listeners\Squawk;

use App\BaseFunctionalTestCase;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Events\SquawkUnassignedEvent;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use App\Models\Vatsim\NetworkAircraft;

class ReclaimIfLeftFirProximityTest extends BaseFunctionalTestCase
{
    /**
     * @var ReclaimIfLeftFirProximity
     */
    private $listener;

    public function setUp(): void
    {
        parent::setUp();
        $this->listener = $this->app->make(ReclaimIfLeftFirProximity::class);
    }

    private function callListener(): bool
    {
        return $this->listener->handle(new NetworkAircraftUpdatedEvent(NetworkAircraft::find('BAW123')));
    }

    public function testItDoesNothingIfNoAssignment()
    {
        $this->doesntExpectEvents(SquawkUnassignedEvent::class);
        $this->assertTrue($this->callListener());
    }

    public function testItDoesNothingIfWithinProximity()
    {
        $this->doesntExpectEvents(SquawkUnassignedEvent::class);
        CcamsSquawkAssignment::create(
            [
                'callsign' => 'BAW123',
                'code' => '0101'
            ]
        );
        NetworkAircraft::where('callsign', 'BAW123')->update(['latitude' => '54.66', 'longitude'=>'-6.21']);
        $this->assertTrue($this->callListener());
        $this->assertDatabaseHas(
            'ccams_squawk_assignments',
            [
                'callsign' => 'BAW123',
                'code' => '0101'
            ]
        );
    }

    public function testItReclaimsIfAircraftNotInVicinity()
    {
        CcamsSquawkAssignment::create(
            [
                'callsign' => 'BAW123',
                'code' => '0101'
            ]
        );
        NetworkAircraft::where('callsign', 'BAW123')->update(['latitude' => '36.09', 'longitude'=>'-115.15']);
        $this->assertTrue($this->callListener());
        $this->assertDatabaseMissing(
            'ccams_squawk_assignments',
            [
                'callsign' => 'BAW123',
            ]
        );
    }
}
