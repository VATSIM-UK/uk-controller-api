<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Release\Departure\ControllerDepartureReleaseDecision;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Model;

class DepartureReleaseApprovedEventTest extends BaseFunctionalTestCase
{
    private DepartureReleaseApprovedEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->event = new DepartureReleaseApprovedEvent(
            new ControllerDepartureReleaseDecision(
                [
                    'departure_release_request_id' => 1,
                    'controller_position_id' => 2,
                    'release_expires_at' => Carbon::now()->addMinutes(2)
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
        $this->assertEquals('departure_release.approved', $this->event->broadcastAs());
    }

    public function testItHasBroadcastData()
    {
        $expected = [
            'id' => 1,
            'controller_position_id' => 2,
            'expires_at' => Carbon::now()->addMinutes(2)->toDateTimeString(),
            'released_at' => null,
        ];

        $this->assertEquals($expected, $this->event->broadcastWith());
    }

    public function testItHasBroadcastDataWithReleaseAtTime()
    {
        $event2 = new DepartureReleaseApprovedEvent(
            new ControllerDepartureReleaseDecision(
                [
                    'departure_release_request_id' => 1,
                    'controller_position_id' => 2,
                    'release_expires_at' => Carbon::now()->addMinutes(2),
                    'release_valid_from' => Carbon::now()->addMinute(),
                ]
            )
        );
        $expected = [
            'id' => 1,
            'controller_position_id' => 2,
            'expires_at' => Carbon::now()->addMinutes(2)->toDateTimeString(),
            'released_at' => Carbon::now()->addMinute()->toDateTimeString(),
        ];

        $this->assertEquals($expected, $event2->broadcastWith());
    }
}
