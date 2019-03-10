<?php

use App\BaseFunctionalTestCase;
use App\Helpers\MinStack\DirectMinStackCalculation;
use App\Helpers\MinStack\MinStackDataProviderInterface;
use App\Services\MetarService;
use Mockery\MockInterface;

class DirectMinStackCalculationTest extends BaseFunctionalTestCase
{
    /**
     * @var MockInterface
     */
    private $metarService;

    /**
     * @var MockInterface
     */
    private $dataProvider;

    /**
     * @var DirectMinStackCalculation
     */
    private $calculation;

    public function setUp() : void
    {
        parent::setUp();
        $this->metarService = Mockery::mock(MetarService::class);
        $this->dataProvider = Mockery::mock(MinStackDataProviderInterface::class);
        $this->calculation = new DirectMinStackCalculation($this->dataProvider, $this->metarService);

        $this->dataProvider->shouldReceive('calculationFacility')->andReturn('EGLL');
        $this->dataProvider->shouldReceive('transitionAltitude')->andReturn(6000);
        $this->dataProvider->shouldReceive('standardPressureHigh')->andReturn(true);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(DirectMinStackCalculation::class, $this->calculation);
    }

    public function testItReturnsNullIfMetarServiceFails()
    {
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->once()->with('EGLL')->andReturn(null);
        $this->assertNull($this->calculation->calculateMinStack());
    }

    public function testItReturnsMinStack()
    {
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->once()->with('EGLL')->andReturn(1013);
        $this->assertEquals(7000, $this->calculation->calculateMinStack());
    }
}
