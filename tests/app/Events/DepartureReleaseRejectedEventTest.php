<?php

namespace App\Events;

use App\BaseUnitTestCase;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseRejectedEventTest extends BaseUnitTestCase
{
    private DepartureReleaseRejectedEvent $event;
    private DepartureReleaseRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->request = new DepartureReleaseRequest(
            [
                'controller_position_id' => 2,
                'release_expires_at' => Carbon::now()->addMinutes(2),
                'remarks' => 'Remarks',
            ]
        );
        $this->request->id = 1;
        $this->event = new DepartureReleaseRejectedEvent($this->request);
    }

    public function testItBroadcastsOnChannel()
    {
        $this->assertEquals([new PrivateChannel('departure-releases')], $this->event->broadcastOn());
    }

    public function testItHasABroadcastName()
    {
        $this->assertEquals('departure_release.rejected', $this->event->broadcastAs());
    }

    public function testItHasBroadcastData()
    {
        $expected = [
            'id' => 1,
            'remarks' => 'Remarks',
        ];

        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
