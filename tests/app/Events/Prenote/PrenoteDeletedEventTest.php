<?php

namespace App\Events\Prenote;

use App\BaseUnitTestCase;
use App\Models\Prenote\PrenoteMessage;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;

class PrenoteDeletedEventTest extends BaseUnitTestCase
{
    private PrenoteMessage $message;
    private PrenoteDeletedEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->message = new PrenoteMessage(
            [
                'callsign' => 'BAW123',
                'departure_airfield' => 'EGLL',
                'departure_sid' => 'MODMI1G',
                'destination_airfield' => 'EGJJ',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addSeconds(15)
            ]
        );
        $this->message->id = 55;
        $this->event = new PrenoteDeletedEvent($this->message);
    }

    public function testItBroadcastsOnTheCorrectChannel()
    {
        $this->assertEquals([new PrivateChannel('prenote-messages')], $this->event->broadcastOn());
    }

    public function testItBroadcastsTheEvent()
    {
        $this->assertEquals('prenote-message.deleted', $this->event->broadcastAs());
    }

    public function testItHasPayload()
    {
        $expected = [
            'id' => 55,
        ];
        $this->assertEquals($expected, $this->event->broadcastWith());
    }
}
