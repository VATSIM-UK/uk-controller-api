<?php

namespace App\Helpers\Squawks;

use App\BaseUnitTestCase;

class SquawkAllocationTest extends BaseUnitTestCase
{
    public function testItConstructs()
    {
        $instance = new SquawkAllocation("1234", true);
        $this->assertInstanceOf(SquawkAllocation::class, $instance);
    }

    public function testItHasASquawk()
    {
        $allocation = new SquawkAllocation("1234", true);
        $this->assertSame("1234", $allocation->squawk());
    }

    public function testItKnowsWhetherItIsNew()
    {
        $allocation = new SquawkAllocation("1234", true);
        $this->assertTrue($allocation->isNewAllocation());
    }
}
