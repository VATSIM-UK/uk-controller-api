<?php

namespace App\Events\MissedApproach;

use App\BaseFunctionalTestCase;
use App\Models\MissedApproach\MissedApproachNotification;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;

class MissedApproachEventTest extends BaseFunctionalTestCase
{
    private MissedApproachEvent $event;
    private MissedApproachNotification $approach;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->approach = MissedApproachNotification::create(
            [
                'callsign' => 'BAW123',
                'user_id' => self::ACTIVE_USER_CID,
                'expires_at' => Carbon::now()->startOfSecond()->addMinutes(3),
            ]
        );
        $this->event = new MissedApproachEvent($this->approach);
    }

    public function testItBroadcastsOnTheCorrectChannel()
    {
        $this->assertEquals([new PrivateChannel('missed-approaches')], $this->event->broadcastOn());
    }

    public function testItBroadcastsTheEvent()
    {
        $this->assertEquals('missed-approach.created', $this->event->broadcastAs());
    }

    public function testItHasPayload()
    {
        $expected = [
            'id' => $this->approach->id,
            'callsign' => 'BAW123',
            'expires_at' => Carbon::now()->startOfSecond()->addMinutes(3)->toDateTimeString(),
        ];
        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
