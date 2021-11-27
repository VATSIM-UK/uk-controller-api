<?php

namespace App\Events\MissedApproach;

use App\BaseFunctionalTestCase;
use App\Models\MissedApproach\MissedApproachNotification;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;

class MissedApproachAcknowledgedEventTest extends BaseFunctionalTestCase
{
    private MissedApproachAcknowledgedEvent $event;
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
        $this->approach->remarks = 'Foo';

        $this->event = new MissedApproachAcknowledgedEvent($this->approach, 'LON_S_CTR');
    }

    public function testItBroadcastsOnTheCorrectChannel()
    {
        $this->assertEquals([new PrivateChannel('missed-approaches')], $this->event->broadcastOn());
    }

    public function testItBroadcastsTheEvent()
    {
        $this->assertEquals('missed-approach.acknowledged', $this->event->broadcastAs());
    }

    public function testItHasPayload()
    {
        $expected = [
            'id' => $this->approach->id,
            'acknowledged_by' => 'LON_S_CTR',
            'remarks' => 'Foo',
        ];
        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
