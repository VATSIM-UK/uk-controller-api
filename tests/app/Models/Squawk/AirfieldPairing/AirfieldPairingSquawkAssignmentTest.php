<?php

namespace App\Models\Squawk\AirfieldPairing;

use App\BaseFunctionalTestCase;

class AirfieldPairingSquawkAssignmentTest extends BaseFunctionalTestCase
{
    public function testItReturnsTheAssignedCode()
    {
        $assignment = new AirfieldPairingSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('0101', $assignment->getCode());
    }

    public function testItReturnsTheAssignmentType()
    {
        $assignment = new AirfieldPairingSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('AIRFIELD_PAIR', $assignment->getType());
    }

    public function testItReturnsTheCallsign()
    {
        $assignment = new AirfieldPairingSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('BAW123', $assignment->getCallsign());
    }
}
