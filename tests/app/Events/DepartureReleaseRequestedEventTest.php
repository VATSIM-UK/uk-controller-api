<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;

class DepartureReleaseRequestedEventTest extends BaseFunctionalTestCase
{
    private DepartureReleaseRequestedEvent $event;
    private DepartureReleaseRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->request = DepartureReleaseRequest::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        $this->event = new DepartureReleaseRequestedEvent($this->request);
    }

    public function testItBroadcastsOnChannel()
    {
        $this->assertEquals([new PrivateChannel('departure-releases')], $this->event->broadcastOn());
    }

    public function testItHasABroadcastName()
    {
        $this->assertEquals('departure_release.requested', $this->event->broadcastAs());
    }

    public function testItHasBroadcastData()
    {
        $expected = [
            'id' => $this->request->id,
            'callsign' => 'BAW123',
            'expires_at' => Carbon::now()->addMinutes(2)->toDateTimeString(),
            'requesting_controller' => 1,
            'target_controller' => 2,
        ];

        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
