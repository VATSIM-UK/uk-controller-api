<?php

use App\BaseFunctionalTestCase;
use App\Helpers\MinStack\LowestMinStackCalculation;
use App\Helpers\MinStack\MinStackDataProviderInterface;
use App\Services\MetarService;
use Mockery\MockInterface;

class LowestMinStackCalculationTest extends BaseFunctionalTestCase
{
    /**
     * @var MockInterface
     */
    private $metarService;

    /**
     * @var MockInterface
     */
    private $dataProvider1;

    /**
     * @var MockInterface
     */
    private $dataProvider2;

    /**
     * @var LowestMinStackCalculation
     */
    private $calculation;

    public function setUp() : void
    {
        parent::setUp();
        $this->metarService = Mockery::mock(MetarService::class);
        $this->dataProvider1 = Mockery::mock(MinStackDataProviderInterface::class);
        $this->dataProvider2 = Mockery::mock(MinStackDataProviderInterface::class);
        $this->calculation = new LowestMinStackCalculation(
            $this->metarService,
            $this->dataProvider1,
            $this->dataProvider2
        );

        // Prepare the providers
        $this->dataProvider1->shouldReceive('calculationFacility')->andReturn('EGLL');
        $this->dataProvider1->shouldReceive('transitionAltitude')->andReturn(6000);
        $this->dataProvider1->shouldReceive('standardPressureHigh')->andReturn(true);

        $this->dataProvider2->shouldReceive('calculationFacility')->andReturn('EGLC');
        $this->dataProvider2->shouldReceive('transitionAltitude')->andReturn(6000);
        $this->dataProvider2->shouldReceive('standardPressureHigh')->andReturn(true);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(LowestMinStackCalculation::class, $this->calculation);
    }

    public function testItReturnsNullIfNoMetars()
    {
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->once()->with('EGLL')->andReturn(null);
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->once()->with('EGLC')->andReturn(null);
        $this->assertNull($this->calculation->calculateMinStack());
    }

    public function testItHandlesMissingMetars()
    {
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->once()->with('EGLL')->andReturn(1014);
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->once()->with('EGLC')->andReturn(null);
        $this->assertEquals(7000, $this->calculation->calculateMinStack());
    }

    public function testItUsesTheLowestQnhToCalculateMinStack()
    {
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->once()->with('EGLL')->andReturn(1014);
        $this->metarService->shouldReceive('getQnhFromVatsimMetar')->once()->with('EGLC')->andReturn(1012);
        $this->assertEquals(8000, $this->calculation->calculateMinStack());
    }
}
