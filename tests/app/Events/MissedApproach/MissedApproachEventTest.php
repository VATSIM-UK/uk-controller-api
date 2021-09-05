<?php

namespace App\Events\MissedApproach;

use App\BaseUnitTestCase;
use App\Models\MissedApproach\MissedApproachNotification;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;

class MissedApproachEventTest extends BaseUnitTestCase
{
    private MissedApproachNotification $missedApproach;
    private MissedApproachEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->event = new MissedApproachEvent(
            new MissedApproachNotification(['callsign' => 'BAW123', 'user_id' => self::ACTIVE_USER_CID])
        );
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
            'callsign' => 'BAW123',
        ];
        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
