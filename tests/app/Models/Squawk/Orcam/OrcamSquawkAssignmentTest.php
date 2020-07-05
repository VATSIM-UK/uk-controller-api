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

    public function testItReturnsTheAssignemntType()
    {
        $assignment = new OrcamSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('ORCAM', $assignment->getType());
    }
}
