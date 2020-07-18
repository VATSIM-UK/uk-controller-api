<?php

namespace App\Console\Commands;

use App\BaseUnitTestCase;
use App\Events\RegionalPressuresUpdatedEvent;
use Mockery;
use App\Services\RegionalPressureService;
use Illuminate\Support\Facades\Artisan;

class GenerateRegionalPressuresTest extends BaseUnitTestCase
{
    const ARTISAN_COMMAND = 'regional:generate';
    
    /**
     * Command under test
     *
     * @var GenerateRegionalPressures
     */
    private $command;

    public function setUp() : void
    {
        parent::setUp();
        $this->command = $this->app->make(GenerateRegionalPressures::class);
    }

    public function testCommandSuccess()
    {
        $service = Mockery::mock(RegionalPressureService::class);
        $service->shouldReceive('generateRegionalPressures')->andReturn(['test' => 1012]);
        $this->app[RegionalPressureService::class] = $service;
        $this->expectsEvents(RegionalPressuresUpdatedEvent::class);

        $this->assertEquals(0, Artisan::call(self::ARTISAN_COMMAND));
    }

    public function testCommandFailureNoPressures()
    {
        $service = Mockery::mock(RegionalPressureService::class);
        $service->shouldReceive('generateRegionalPressures')->andReturn([]);
        $service->shouldReceive('getLastError')->andReturn('Test');
        $this->app[RegionalPressureService::class] = $service;

        $this->assertEquals(1, Artisan::call(self::ARTISAN_COMMAND));
    }

    public function testCommandFailure()
    {
        $service = Mockery::mock(RegionalPressureService::class);
        $service->shouldReceive('generateRegionalPressures')->andReturn(null);
        $service->shouldReceive('getLastError')->andReturn('Test');
        $this->app[RegionalPressureService::class] = $service;

        $this->assertEquals(1, Artisan::call(self::ARTISAN_COMMAND));
    }
}
