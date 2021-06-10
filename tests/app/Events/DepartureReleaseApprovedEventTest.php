<?php

namespace App\Events;

use App\BaseUnitTestCase;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Model;

class DepartureReleaseApprovedEventTest extends BaseUnitTestCase
{
    private DepartureReleaseApprovedEvent $event;
    private DepartureReleaseRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->request = new DepartureReleaseRequest(
            [
                'controller_position_id' => 2,
                'release_expires_at' => Carbon::now()->addMinutes(2),
                'release_valid_from' => Carbon::now()->addMinute(),
            ]
        );
        $this->request->id = 1;
        $this->event = new DepartureReleaseApprovedEvent($this->request);
    }

    public function testItBroadcastsOnChannel()
    {
        $this->assertEquals([new PrivateChannel('departure-releases')], $this->event->broadcastOn());
    }

    public function testItHasABroadcastName()
    {
        $this->assertEquals('departure_release.approved', $this->event->broadcastAs());
    }

    public function testItHasBroadcastData()
    {
        $expected = [
            'id' => 1,
            'expires_at' => Carbon::now()->addMinutes(2)->toDateTimeString(),
            'released_at' => Carbon::now()->addMinute()->toDateTimeString(),
        ];

        $this->assertEquals($expected, $this->event->broadcastWith());
    }

    public function testItHasBroadcastWithNoApprovalTime()
    {
        $this->request->release_expires_at = null;
        $expected = [
            'id' => 1,
            'expires_at' => null,
            'released_at' => Carbon::now()->addMinute()->toDateTimeString(),
        ];

        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
