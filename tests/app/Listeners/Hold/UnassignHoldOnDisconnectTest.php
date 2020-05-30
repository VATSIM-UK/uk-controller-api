<?php

namespace App\Listeners\Hold;

use App\BaseUnitTestCase;
use App\Events\HoldUnassignedEvent;

class UnassignHoldOnDisconnectTest extends BaseUnitTestCase
{
    public function testItTriggersAHoldUnassignedEvent()
    {
        $this->expectsEvents(new HoldUnassignedEvent('BAW123'));
        $event =  new UnassignHoldOnDisconnect();
        $event->handle('BAW123');
    }
}
