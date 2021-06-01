<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseRequestCancelledEventTest extends BaseFunctionalTestCase
{
    private DepartureReleaseRequestCancelledEvent $event;
    private DepartureReleaseRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->request = new DepartureReleaseRequest();
        $this->request->id = 1;
        $this->event = new DepartureReleaseRequestCancelledEvent($this->request);
    }

    public function testItBroadcastsOnChannel()
    {
        $this->assertEquals([new PrivateChannel('departure-releases')], $this->event->broadcastOn());
    }

    public function testItHasABroadcastName()
    {
        $this->assertEquals('departure_release.request_cancelled', $this->event->broadcastAs());
    }

    public function testItHasBroadcastData()
    {
        $expected = [
            'id' => 1,
        ];

        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
