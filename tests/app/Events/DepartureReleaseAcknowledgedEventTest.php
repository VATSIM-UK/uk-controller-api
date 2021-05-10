<?php

namespace App\Events;

use App\BaseUnitTestCase;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseAcknowledgedEventTest extends BaseUnitTestCase
{
    private DepartureReleaseAcknowledgedEvent $event;
    private DepartureReleaseRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->request = new DepartureReleaseRequest(
            [
                'controller_position_id' => 2,
                'release_expires_at' => Carbon::now()->addMinutes(2)
            ]
        );
        $this->request->id = 1;
        $this->event = new DepartureReleaseAcknowledgedEvent($this->request);
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
        ];

        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
