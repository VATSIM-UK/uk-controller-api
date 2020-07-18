<?php

namespace App\Models\Squawk\UnitDiscrete;

use App\BaseFunctionalTestCase;

class UnitDiscreteSquawkAssignmentTest extends BaseFunctionalTestCase
{
    public function testItReturnsTheAssignedCode()
    {
        $assignment = new UnitDiscreteSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('0101', $assignment->getCode());
    }

    public function testItReturnsTheAssignmentType()
    {
        $assignment = new UnitDiscreteSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('UNIT_DISCRETE', $assignment->getType());
    }

    public function testItReturnsTheCallsign()
    {
        $assignment = new UnitDiscreteSquawkAssignment(
            [
                'unit' => 'EGGD',
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('BAW123', $assignment->getCallsign());
    }
}
