<?php

namespace App\Models\Squawk\Orcam;

use App\BaseFunctionalTestCase;

class OrcamSquawkAssignmentTest extends BaseFunctionalTestCase
{
    public function testItReturnsTheAssignedCode()
    {
        $assignment = new OrcamSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('0101', $assignment->getCode());
    }

    public function testItReturnsTheAssignmentType()
    {
        $assignment = new OrcamSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('ORCAM', $assignment->getType());
    }

    public function testItReturnsTheCallsign()
    {
        $assignment = new OrcamSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('BAW123', $assignment->getCallsign());
    }
}
