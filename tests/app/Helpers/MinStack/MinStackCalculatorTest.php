<?php

namespace App\Helpers\MinStack;

use App\BaseFunctionalTestCase;
use Mockery;
use Mockery\MockInterface;

class MinStackCalculatorTest extends BaseFunctionalTestCase
{
    /**
     * @var MockInterface
     */
    private $minStackProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->minStackProvider = Mockery::mock(MinStackDataProviderInterface::class);
    }

    public function testItReturnsTransitionPlus1000OnHighPressure()
    {
        $this->minStackProvider->shouldReceive('transitionAltitude')
            ->andReturn(6000);
        $this->minStackProvider->shouldReceive('standardPressureHigh')
            ->andReturn(true);
        $this->assertEquals(7000, MinStackCalculator::calculateMinStack($this->minStackProvider, 1014));
    }

    public function testItReturnsTransitionPlus1000OnBoundaryWithHighPressureTrue()
    {
        $this->minStackProvider->shouldReceive('transitionAltitude')
            ->andReturn(6000);
        $this->minStackProvider->shouldReceive('standardPressureHigh')
            ->andReturn(true);
        $this->assertEquals(7000, MinStackCalculator::calculateMinStack($this->minStackProvider, 1013));
    }

    public function testItReturnsTransitionPlus2000OnBoundaryWithHighPressureFalse()
    {
        $this->minStackProvider->shouldReceive('transitionAltitude')
            ->andReturn(6000);
        $this->minStackProvider->shouldReceive('standardPressureHigh')
            ->andReturn(false);
        $this->assertEquals(8000, MinStackCalculator::calculateMinStack($this->minStackProvider, 1013));
    }

    public function testItReturnsTransitionPlus2000OnLowPressureBoundary()
    {
        $this->minStackProvider->shouldReceive('transitionAltitude')
            ->andReturn(6000);
        $this->minStackProvider->shouldReceive('standardPressureHigh')
            ->andReturn(false);
        $this->assertEquals(8000, MinStackCalculator::calculateMinStack($this->minStackProvider, 978));
    }

    public function testItReturnsTransitionPlus3000OnDoubleLow()
    {
        $this->minStackProvider->shouldReceive('transitionAltitude')
            ->andReturn(6000);
        $this->minStackProvider->shouldReceive('standardPressureHigh')
            ->andReturn(true);
        $this->assertEquals(9000, MinStackCalculator::calculateMinStack($this->minStackProvider, 955));
    }
}
