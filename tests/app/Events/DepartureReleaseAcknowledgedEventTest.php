<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Release\Departure\ControllerDepartureReleaseDecision;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseAcknowledgedEventTest extends BaseFunctionalTestCase
{
    private DepartureReleaseAcknowledgedEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->event = new DepartureReleaseAcknowledgedEvent(
            new ControllerDepartureReleaseDecision(
                [
                    'departure_release_request_id' => 1,
                    'controller_position_id' => 2,
                ]
            )
        );
    }

    public function testItBroadcastsOnChannel()
    {
        $this->assertEquals([new PrivateChannel('departure-releases')], $this->event->broadcastOn());
    }

    public function testItHasABroadcastName()
    {
        $this->assertEquals('departure_release.acknowledged', $this->event->broadcastAs());
    }

    public function testItHasBroadcastData()
    {
        $expected = [
            'id' => 1,
            'controller_position_id' => 2,
        ];

        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
