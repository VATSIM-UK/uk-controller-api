<?php

namespace App\Events;

use App\BaseFunctionalTestCase;

class HoldUnassignedEventTest extends BaseFunctionalTestCase
{
    public function testItBroadcastsWithData()
    {
        $event = new HoldUnassignedEvent('BAW123');
        $this->assertEquals(['callsign' => 'BAW123'], $event->broadcastWith());
    }
}
