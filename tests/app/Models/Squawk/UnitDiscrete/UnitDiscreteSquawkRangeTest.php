<?php

namespace App\Models\Squawk\UnitDiscrete;

use App\BaseFunctionalTestCase;

class UnitDiscreteSquawkRangeTest extends BaseFunctionalTestCase
{
    public function testItReturnsFirstSquawkInRange()
    {
        $range = UnitDiscreteSquawkRange::create(
            [
                'unit' => 'EGGD',
                'first' => '0101',
                'last' => '0102',
            ]
        );

        $this->assertEquals('0101', $range->first());
    }

    public function testItReturnsLastSquawkInRange()
    {
        $range = UnitDiscreteSquawkRange::create(
            [
                'unit' => 'EGGD',
                'first' => '0101',
                'last' => '0102',
            ]
        );

        $this->assertEquals('0102', $range->last());
    }
}
