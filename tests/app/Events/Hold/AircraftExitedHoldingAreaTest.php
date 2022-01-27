<?php

namespace App\Events\Hold;

use App\BaseFunctionalTestCase;
use App\Events\HighPriorityBroadcastEvent;
use App\Models\Navigation\Navaid;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Broadcasting\PrivateChannel;

class AircraftExitedHoldingAreaTest extends BaseFunctionalTestCase
{
    private AircraftExitedHoldingArea $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->event = new AircraftExitedHoldingArea(
            NetworkAircraft::find('BAW123'),
            Navaid::find(1)
        );
    }

    public function testItsAHighPriorityBroadcastEvent()
    {
        $this->assertInstanceOf(HighPriorityBroadcastEvent::class, $this->event);
    }

    public function testItBroadcastsOnTheCorrectChannel()
    {
        $this->assertEquals([new PrivateChannel('holds')], $this->event->broadcastOn());
    }

    public function testItBroadcastsTheEvent()
    {
        $this->assertEquals('hold.area-exited', $this->event->broadcastAs());
    }

    public function testItHasPayload()
    {
        $expected = [
            'callsign' => 'BAW123',
            'navaid_id' => 1,
        ];
        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
