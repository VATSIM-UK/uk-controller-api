<?php

namespace App\Console\Commands;

use App\BaseUnitTestCase;
use Mockery;
use App\Services\RegionalPressureService;
use Illuminate\Support\Facades\Artisan;

class GenerateRegionalPressuresTest extends BaseUnitTestCase
{
    /**
     * Command under test
     *
     * @var GenerateRegionalPressures
     */
    private $command;

    /**
     * Mock output
     *
     * @var OutputInterface;
     */
    private $outputMock;

    public function setUp()
    {
        parent::setUp();
        $this->command = $this->app->make(GenerateRegionalPressures::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(GenerateRegionalPressures::class, $this->command);
    }

    public function testCommandSuccess()
    {
        $service = Mockery::mock(RegionalPressureService::class);
        $service->shouldReceive('generateRegionalPressures')->andReturn(true);
        $this->app[RegionalPressureService::class] = $service;
        
        $this->assertEquals(0, Artisan::call('regionals:generate'));
    }

    public function testCommandFailure()
    {
        $service = Mockery::mock(RegionalPressureService::class);
        $service->shouldReceive('generateRegionalPressures')->andReturn(false);
        $this->app[RegionalPressureService::class] = $service;
        
        $this->assertEquals(1, Artisan::call('regionals:generate'));
    }
}
