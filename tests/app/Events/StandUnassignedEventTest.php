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
        $this->assertEquals(
            [
                'callsign' => 'BAW123',
                'stand_id' => null,
                'assigned_by_reservation_allocator' => false,
                'assigned_by_pilot_request' => false,
                'assignment_source' => 'system_auto',
                'assignment_status' => 'assigned',
            ],
            $this->event->broadcastWith()
        );
    }

    public function testItBroadcastsOnChannel()
    {
        $this->assertEquals([new PrivateChannel('stand-assignments')], $this->event->broadcastOn());
    }
}
