<?php

namespace App\Events\Hold;

use App\BaseFunctionalTestCase;
use App\Events\HighPriorityBroadcastEvent;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;

class AircraftEnteredHoldingAreaTest extends BaseFunctionalTestCase
{
    private AircraftEnteredHoldingArea $event;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
        NetworkAircraft::find('BAW123')
            ->proximityNavaids()
            ->sync([1 => ['entered_at' => Carbon::now()]]);

        $this->event = new AircraftEnteredHoldingArea(
            NetworkAircraft::find('BAW123'),
            NetworkAircraft::find('BAW123')->proximityNavaids->first()
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
        $this->assertEquals('hold.area-entered', $this->event->broadcastAs());
    }

    public function testItHasPayload()
    {
        $expected = [
            'callsign' => 'BAW123',
            'navaid_id' => 1,
            'entered_at' => Carbon::now(),
        ];
        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
