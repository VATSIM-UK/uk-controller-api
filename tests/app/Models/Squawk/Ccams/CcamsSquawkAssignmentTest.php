<?php

namespace App\Models\Squawk\Ccams;

use App\BaseFunctionalTestCase;

class CcamsSquawkAssignmentTest extends BaseFunctionalTestCase
{
    public function testItReturnsTheAssignedCode()
    {
        $assignment = new CcamsSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('0101', $assignment->getCode());
    }

    public function testItReturnsTheAssignmentType()
    {
        $assignment = new CcamsSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('CCAMS', $assignment->getType());
    }

    public function testItReturnsTheCallsign()
    {
        $assignment = new CcamsSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('BAW123', $assignment->getCallsign());
    }
}
