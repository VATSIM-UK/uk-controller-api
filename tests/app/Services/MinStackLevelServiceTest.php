<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use Illuminate\Support\Carbon;
use Mockery;

class MinStackLevelServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var MinStackLevelService
     */
    private $service;

    public function setUp() : void
    {
        parent::setUp();
        $this->service = $this->app->make(MinStackLevelService::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(MinStackLevelService::class, $this->service);
    }

    public function testItReturnsAirfieldMinStacks()
    {
        $this->assertEquals(7000, $this->service->getMinStackLevelForAirfield("EGLL"));
    }

    public function testItReturnsNullMinStackAirfieldNotFound()
    {
        $this->assertNull($this->service->getMinStackLevelForAirfield("EGXY"));
    }

    public function testItReturnsNullMinStackAirfieldHasNoMinStack()
    {
        $this->assertNull($this->service->getMinStackLevelForAirfield("EGBB"));
    }

    public function testItReturnsTmaMinStacks()
    {
        $this->assertEquals(6000, $this->service->getMinStackLevelForTma("MTMA"));
    }

    public function testItReturnsNullMinStackTmaNotFound()
    {
        $this->assertNull($this->service->getMinStackLevelForTma("STMA"));
    }

    public function testItReturnsNullMinStackTmaHasNoMinStack()
    {
        $this->assertNull($this->service->getMinStackLevelForTma("LTMA"));
    }

    public function testItReturnsAllAirfieldMinStackLevels()
    {
        $this->assertEquals(['EGLL' => 7000], $this->service->getAllAirfieldMinStackLevels());
    }

    public function testItReturnsAllTmaMinStackLevels()
    {
        $this->assertEquals(['MTMA' => 6000], $this->service->getAllTmaMinStackLevels());
    }

    public function testItGeneratesMinStackLevelsForAirfields()
    {
        Carbon::setTestNow(Carbon::now());

        // Mock the METAR service because we don't want to make actual calls to VATSIM
        $metarService = Mockery::mock(MetarService::class);
        $metarService->shouldReceive('getQnhFromVatsimMetar')
            ->with('EGLL')
            ->once()
            ->andReturn(1012);

        $metarService->shouldReceive('getQnhFromVatsimMetar')
            ->with('EGBB')
            ->once()
            ->andReturn(1014);

        $metarService->shouldReceive('getQnhFromVatsimMetar')
            ->with('EGKR')
            ->never();

        $this->app->instance(MetarService::class, $metarService);
        $this->service->updateAirfieldMinStackLevelsFromVatsimMetarServer();

        $this->seeInDatabase(
            'msl_airfield',
            [
                'airfield_id' => 1,
                'msl' => 8000,
                'generated_at' => Carbon::now()
            ]
        );

        $this->seeInDatabase(
            'msl_airfield',
            [
                'airfield_id' => 2,
                'msl' => 7000,
                'generated_at' => Carbon::now()
            ]
        );

        $this->notSeeInDatabase(
            'msl_airfield',
            [
                'airfield_id' => 3,
            ]
        );
    }

    public function testItGeneratesMinStackLevelsForTmas()
    {
        Carbon::setTestNow(Carbon::now());

        // Mock the METAR service because we don't want to make actual calls to VATSIM
        $metarService = Mockery::mock(MetarService::class);
        $metarService->shouldReceive('getQnhFromVatsimMetar')
            ->with('EGLL')
            ->once()
            ->andReturn(1014);

        $metarService->shouldReceive('getQnhFromVatsimMetar')
            ->with('EGBB')
            ->once()
            ->andReturn(977);

        $this->app->instance(MetarService::class, $metarService);
        $this->service->updateTmaMinStackLevelsFromVatsimMetarServer();

        $this->seeInDatabase(
            'msl_tma',
            [
                'tma_id' => 1,
                'msl' => 7000,
                'generated_at' => Carbon::now()
            ]
        );

        $this->seeInDatabase(
            'msl_tma',
            [
                'tma_id' => 2,
                'msl' => 9000,
                'generated_at' => Carbon::now()
            ]
        );
    }
}
