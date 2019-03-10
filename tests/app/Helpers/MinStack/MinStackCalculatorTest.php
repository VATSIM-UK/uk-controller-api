<?php

use App\BaseUnitTestCase;
use App\Helpers\MinStackLevel\MinStackCalculator;

class MinStackCalculatorTest extends BaseUnitTestCase
{
    public function testItReturnsTransitionPlus1000OnHighPressure()
    {
        $this->assertEquals(7000, MinStackCalculator::CalculateMinStack(6000, 1014, true));
    }

    public function testItReturnsTransitionPlus1000OnBoundaryWithHighPressureTrue()
    {
        $this->assertEquals(7000, MinStackCalculator::CalculateMinStack(6000, 1013, true));
    }

    public function testItReturnsTransitionPlus2000OnBoundaryWithHighPressureFalse()
    {
        $this->assertEquals(8000, MinStackCalculator::CalculateMinStack(6000, 1013, false));
    }

    public function testItReturnsTransitionPlus2000OnLowPressureBoundary()
    {
        $this->assertEquals(8000, MinStackCalculator::CalculateMinStack(6000, 978, false));
    }

    public function testItReturnsTransitionPlus3000OnDoubleLow()
    {
        $this->assertEquals(9000, MinStackCalculator::CalculateMinStack(6000, 977, false));
    }
}
