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

    public function testItReturnsTheAssignemntType()
    {
        $assignment = new AirfieldPairingSquawkAssignment(
            [
                'callsign' => 'BAW123',
                'code' => '0101',
            ]
        );

        $this->assertEquals('AIRFIELD_PAIR', $assignment->getType());
    }
}
