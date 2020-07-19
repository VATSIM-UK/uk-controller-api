<?php

namespace App\Events;

use App\BaseUnitTestCase;

class GroundStatusAssignedEventTest extends BaseUnitTestCase
{
    public function testItBroadcastsWithData()
    {
        $event = new GroundStatusAssignedEvent('BAW123', 1);
        $this->assertEquals(['BAW123' => 1], $event->broadcastWith());
    }
}
