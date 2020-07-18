<?php

namespace App\Models\Squawk\AirfieldPairing;

use App\BaseFunctionalTestCase;

class AirfieldPairingSquawkRangeTest extends BaseFunctionalTestCase
{
    public function testItReturnsFirstSquawkInRange()
    {
        $range = AirfieldPairingSquawkRange::create(
            [
                'origin' => 'EGGD',
                'destination' => 'EGFF',
                'first' => '0101',
                'last' => '0102',
            ]
        );

        $this->assertEquals('0101', $range->first());
    }

    public function testItReturnsLastSquawkInRange()
    {
        $range = AirfieldPairingSquawkRange::create(
            [
                'origin' => 'EGGD',
                'destination' => 'EGFF',
                'first' => '0101',
                'last' => '0102',
            ]
        );

        $this->assertEquals('0102', $range->last());
    }
}
