<?php

namespace App\Providers;

use App\BaseFunctionalTestCase;
use App\Exceptions\InvalidMslCalculationException;
use App\Helpers\MinStack\MinStackCalculableInterface;
use App\Services\MetarService;
use Mockery;

class MinStackCalculationProviderTest extends BaseFunctionalTestCase
{
    public function testItThrowsAnExceptionIfTypeFieldNotPresent()
    {
        $this->expectException(InvalidMslCalculationException::class);
        $this->app->makeWith(MinStackCalculableInterface::class, ['calculation' => []]);
    }

    public function testItThrowsAnExceptionIfTypeFieldNotCorrect()
    {
        $this->expectException(InvalidMslCalculationException::class);
        $this->app->makeWith(MinStackCalculableInterface::class, ['type' => 'foo']);
    }

    public function testItReturnsADirectCalculation()
    {
        $metarService = Mockery::mock(MetarService::class);
        $metarService->shouldReceive('getQnhFromVatsimMetar')
            ->with('EGLL')
            ->once()
            ->andReturn(1013);

        $this->app->instance(MetarService::class, $metarService);

        $calculation = $this->app->makeWith(
            MinStackCalculableInterface::class,
            ['type' => 'airfield', 'airfield' => 'EGLL']
        );

        $this->assertEquals(7000, $calculation->calculateMinStack());
    }

    public function testItReturnsALowestCalculation()
    {
        $metarService = Mockery::mock(MetarService::class);
        $metarService->shouldReceive('getQnhFromVatsimMetar')
            ->with('EGLL')
            ->once()
            ->andReturn(1013);

        $metarService->shouldReceive('getQnhFromVatsimMetar')
            ->with('EGBB')
            ->once()
            ->andReturn(1012);

        $this->app->instance(MetarService::class, $metarService);

        $calculation = $this->app->makeWith(
            MinStackCalculableInterface::class,
            ['type' => 'lowest', 'airfields' => ['EGLL', 'EGBB']]
        );

        $this->assertEquals(8000, $calculation->calculateMinStack());
    }
}
