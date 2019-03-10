<?php

use App\BaseFunctionalTestCase;
use App\Helpers\MinStackLevel\MinStackCalculator;
use App\Services\MetarService;
use Mockery\MockInterface;

class MinStackCalculatorTest extends BaseFunctionalTestCase
{
    /**
     * @var MockInterface
     */
    private $metarService;

    /**
     * @var MinStackCalculator
     */
    private $calculator;

    public function setUp(): void
    {
        parent::setUp();
        $this->metarService = Mockery::mock(MetarService::class);
        $this->calculator = new MinStackCalculator($this->metarService);
    }

    public function testItReturnsTransitionPlus1000OnHighPressure()
    {
        $this->assertEquals(7000, MinStackCalculator::calculateMinStack(6000, 1014, true));
    }

    public function testItReturnsTransitionPlus1000OnBoundaryWithHighPressureTrue()
    {
        $this->assertEquals(7000, MinStackCalculator::calculateMinStack(6000, 1013, true));
    }

    public function testItReturnsTransitionPlus2000OnBoundaryWithHighPressureFalse()
    {
        $this->assertEquals(8000, MinStackCalculator::calculateMinStack(6000, 1013, false));
    }

    public function testItReturnsTransitionPlus2000OnLowPressureBoundary()
    {
        $this->assertEquals(8000, MinStackCalculator::calculateMinStack(6000, 978, false));
    }

    public function testItReturnsTransitionPlus3000OnDoubleLow()
    {
        $this->assertEquals(9000, MinStackCalculator::calculateMinStack(6000, 977, false));
    }

    public function testItCalculatesDirectMinStack()
    {
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->with('EGLL')->andReturn(1015);
        $this->assertEquals(7000, $this->calculator->calculateDirectMinStack('EGLL', 6000, true));
    }

    public function testItReturnsNullIfNoDirectMinStackQNH()
    {
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->with('EGLL')->andReturn(null);
        $this->assertNull($this->calculator->calculateDirectMinStack('EGLL', 6000, true));
    }

    public function testItCalculatesLowestMinStack()
    {
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->with('EGLL')->andReturn(1015);
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->with('EGBB')->andReturn(1012);
        $this->assertEquals(8000, $this->calculator->calculateLowestQnhMinStack(['EGLL', 'EGBB']));
    }

    public function testItReturnsNullNoQnhsAvailable()
    {
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->with('EGLL')->andReturn(null);
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->with('EGBB')->andReturn(null);
        $this->assertNull($this->calculator->calculateLowestQnhMinStack(['EGLL', 'EGBB']));
    }

    public function testItHandlesMissingQnhsWhenDoingLowest()
    {
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->with('EGLL')->andReturn(1013);
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->with('EGBB')->andReturn(null);
        $this->assertEquals(7000, $this->calculator->calculateLowestQnhMinStack(['EGLL', 'EGBB']));
    }

    public function testItReturnsNullForNonExistantAirfieldsWhenDoingLowest()
    {
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->with('EGLL')->andReturn(1013);
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->with('EGGG')->andReturn(1012);
        $this->assertNull($this->calculator->calculateLowestQnhMinStack(['EGLL', 'EGGG']));
    }
}
