<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use App\Models\Squawks\Allocation;

class SquawkUnassignedEventTest extends BaseFunctionalTestCase
{
    /**
     * @var SquawkUnassignedEvent
     */
    private $event;

    /**
     * @var Allocation
     */
    private $assignment;

    public function setUp() : void
    {
        parent::setUp();
        $this->assignment = new CcamsSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '7213',
            ]
        );
        $this->event = new SquawkUnassignedEvent($this->assignment);
    }

    public function testItGetsDeletedAssignment()
    {
        $this->assertEquals($this->assignment, $this->event->getDeletedAssignment());
    }
}
