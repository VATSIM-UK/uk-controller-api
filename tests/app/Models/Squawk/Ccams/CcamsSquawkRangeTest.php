<?php

namespace App\Models\Squawk\Ccams;

use App\BaseFunctionalTestCase;

class CcamsSquawkRangeTest extends BaseFunctionalTestCase
{
    public function testItReturnsFirstSquawkInRange()
    {
        $range = CcamsSquawkRange::create(
            [
                'first' => '0101',
                'last' => '0102',
            ]
        );

        $this->assertEquals('0101', $range->first());
    }

    public function testItReturnsLastSquawkInRange()
    {
        $range = CcamsSquawkRange::create(
            [
                'first' => '0101',
                'last' => '0102',
            ]
        );

        $this->assertEquals('0102', $range->last());
    }
}
