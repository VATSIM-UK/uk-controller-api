<?php

namespace App\Events;

use App\BaseUnitTestCase;
use App\Models\Stand\StandAssignment;
use Illuminate\Broadcasting\PrivateChannel;

class StandAssignedEventTest extends BaseUnitTestCase
{
    /**
     * @var StandAssignedEvent
     */
    private $event;

    /**
     * @var StandAssignment
     */
    private $assignment;

    public function setUp(): void
    {
        parent::setUp();
        $this->assignment = new StandAssignment(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );
        $this->event = new StandAssignedEvent($this->assignment);
    }

    public function testItBroadcastsWithData()
    {
        $this->assertEquals(['callsign' => 'BAW123', 'stand_id' => 1], $this->event->broadcastWith());
    }

    public function testItBroadcastsOnChannel()
    {
        $this->assertEquals([new PrivateChannel('stand-assignments')], $this->event->broadcastOn());
    }

    public function testItReturnsAssignment()
    {
        $this->assertEquals($this->assignment, $this->event->getStandAssignment());
    }
}
