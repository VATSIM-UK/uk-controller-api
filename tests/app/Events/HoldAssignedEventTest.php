<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Hold\AssignedHold;

class HoldAssignedEventTest extends BaseFunctionalTestCase
{
    public function testItBroadcastsWithData()
    {
        $data = new AssignedHold(
            [
                'callsign' => 'BAW123',
                'navaid_id' => 1,
            ]
        );

        $event = new HoldAssignedEvent($data);
        $this->assertEquals(['callsign' => 'BAW123', 'navaid' => 'WILLO'], $event->broadcastWith());
    }
}
