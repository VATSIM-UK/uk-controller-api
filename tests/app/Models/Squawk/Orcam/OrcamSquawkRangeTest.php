<?php

namespace App\Models\Squawk\Orcam;

use App\BaseFunctionalTestCase;

class OrcamSquawkRangeTest extends BaseFunctionalTestCase
{
    public function testItReturnsFirstSquawkInRange()
    {
        $range = new OrcamSquawkRange(
            [
                'first' => '0101',
                'last' => '0102',
            ]
        );

        $this->assertEquals('0101', $range->first());
    }

    public function testItReturnsLastSquawkInRange()
    {
        $range = new OrcamSquawkRange(
            [
                'first' => '0101',
                'last' => '0102',
            ]
        );

        $this->assertEquals('0102', $range->last());
    }
}
