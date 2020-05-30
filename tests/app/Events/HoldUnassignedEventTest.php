<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Hold\AssignedHold;

class HoldUnassignedEventTest extends BaseFunctionalTestCase
{
    public function testItBroadcastsWithData()
    {
        $data = new AssignedHold(
            [
                'callsign' => 'BAW123',
                'navaid_id' => 1,
            ]
        );

        $event = new HoldUnassignedEvent($data);
        $this->assertEquals(['callsign' => 'BAW123'], $event->broadcastWith());
    }
}
