<?php

namespace App\Events;

use App\BaseFunctionalTestCase;

class SquawkUnassignedEventTest extends BaseFunctionalTestCase
{
    /**
     * @var SquawkUnassignedEvent
     */
    private $event;

    public function setUp() : void
    {
        parent::setUp();
        $this->event = new SquawkUnassignedEvent('BAW123');
    }

    public function testItGetsCallsign()
    {
        $this->assertEquals('BAW123', $this->event->getCallsign());
    }
}
