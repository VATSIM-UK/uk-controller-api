<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use App\Models\Squawks\Allocation;

class SquawkAssignmentEventTest extends BaseFunctionalTestCase
{
    /**
     * @var SquawkAssignmentEvent
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
        $this->event = new SquawkAssignmentEvent($this->assignment);
    }

    public function testItGetsAssignment()
    {
        $this->assertEquals($this->assignment, $this->event->getAssignment());
    }
}
