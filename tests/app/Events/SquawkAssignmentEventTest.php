<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Squawk\SquawkAssignment;

class SquawkAssignmentEventTest extends BaseFunctionalTestCase
{
    private SquawkAssignmentEvent $event;
    private SquawkAssignment $assignment;

    public function setUp() : void
    {
        parent::setUp();
        $this->assignment = new SquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '7213',
                'assignment_type' => 'CCAMS',
            ]
        );
        $this->event = new SquawkAssignmentEvent($this->assignment);
    }

    public function testItGetsAssignment()
    {
        $this->assertEquals($this->assignment, $this->event->getAssignment());
    }
}
