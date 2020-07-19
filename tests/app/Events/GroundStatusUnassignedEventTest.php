<?php

namespace App\Events;

use App\BaseFunctionalTestCase;

class GroundStatusUnassignedEventTest extends BaseFunctionalTestCase
{
    public function testItBroadcastsWithData()
    {
        $event = new GroundStatusUnassignedEvent('BAW123');
        $this->assertEquals(['callsign' => 'BAW123'], $event->broadcastWith());
    }

    public function testItGetsCallsign()
    {
        $event = new GroundStatusUnassignedEvent('BAW123');
        $this->assertEquals('BAW123', $event->getCallsign());
    }
}
