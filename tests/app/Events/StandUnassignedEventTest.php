<?php

namespace App\Events;

use App\BaseUnitTestCase;
use Illuminate\Broadcasting\PrivateChannel;

class StandUnassignedEventTest extends BaseUnitTestCase
{
    /**
     * @var StandUnassignedEvent
     */
    private $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->event = new StandUnassignedEvent('BAW123');
    }

    public function testItBroadcastsWithData()
    {
        $this->assertEquals(['callsign' => 'BAW123'], $this->event->broadcastWith());
    }

    public function testItBroadcastsOnChannel()
    {
        $this->assertEquals([new PrivateChannel('stand-assignments')], $this->event->broadcastOn());
    }
}
