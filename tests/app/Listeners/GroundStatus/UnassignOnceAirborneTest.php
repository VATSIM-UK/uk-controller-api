<?php

namespace App\Listeners\GroundStatus;

use App\BaseFunctionalTestCase;
use App\Events\GroundStatusUnassignedEvent;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Models\Vatsim\NetworkAircraft;

class UnassignOnceAirborneTest extends BaseFunctionalTestCase
{
    const CALLSIGN = 'BAW123';

    /**
     * @var UnassignOnceAirborne
     */
    private $listener;

    public function setUp(): void
    {
        parent::setUp();
        $this->listener = new UnassignOnceAirborne();
    }

    public function testItDoesNothingIfNoStatus()
    {
        $this->assertTrue(
            $this->listener->handle(
                new NetworkAircraftUpdatedEvent(NetworkAircraft::find(self::CALLSIGN))
            )
        );
    }

    /**
     * @dataProvider withinParametersProvider
     * @param int $groundspeed
     * @param int $altitude
     */
    public function testItUnassignsIfWithinParameters(int $groundspeed, int $altitude)
    {
        $this->expectsEvents(GroundStatusUnassignedEvent::class);
        $this->setAircraftDetails(self::CALLSIGN, $groundspeed, $altitude);
        $this->assertTrue(
            $this->listener->handle(
                new NetworkAircraftUpdatedEvent(NetworkAircraft::find(self::CALLSIGN))
            )
        );
        $this->assertDatabaseMissing(
            'ground_status_network_aircraft',
            [
                'callsign' => self::CALLSIGN
            ]
        );
    }

    public function withinParametersProvider(): array
    {
        return [
            [65, 1000], // Both on boundary
            [66, 1001], // Both above boundary
            [64, 1000], // Only altitude on boundary
            [65, 999], // Only airspeed on boundary
            [64, 1001], // Only altitude above boundary
            [66, 999], // Only airspeed above boundary
        ];
    }

    /**
     * @dataProvider outsideParametersProvider
     * @param int $groundspeed
     * @param int $altitude
     */
    public function testItDoesNotUnassignIfOutsideParameters(int $groundspeed, int $altitude)
    {
        $this->doesntExpectEvents(GroundStatusUnassignedEvent::class);
        $this->setAircraftDetails(self::CALLSIGN, $groundspeed, $altitude);
        $this->assertTrue(
            $this->listener->handle(
                new NetworkAircraftUpdatedEvent(NetworkAircraft::find(self::CALLSIGN))
            )
        );
        $this->assertDatabaseHas(
            'ground_status_network_aircraft',
            [
                'callsign' => self::CALLSIGN,
                'ground_status_id' => 1
            ]
        );
    }

    public function outsideParametersProvider(): array
    {
        return [
            [64, 999],
            [0, 0],
            [34, 231],
        ];
    }

    private function setAircraftDetails(string $callsign, int $groundspeed, int $altitude)
    {
        NetworkAircraft::find($callsign)->update(
            ['groundspeed' => $groundspeed, 'altitude' => $altitude]
        );

        NetworkAircraft::find($callsign)->groundStatus()->sync([1]);
    }
}
