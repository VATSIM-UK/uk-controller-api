<?php

namespace App\Console\Commands;

use App\BaseUnitTestCase;
use App\Services\MinStackLevelService;
use Illuminate\Support\Facades\Artisan;
use Mockery;

class GenerateMinStackLevelsTest extends BaseUnitTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(GenerateMinStackLevels::class, $this->app->make(GenerateMinStackLevels::class));
    }

    public function testItUpdatesTheMslsOnce()
    {
        $serviceMock = Mockery::mock(MinStackLevelService::class);
        $serviceMock->shouldReceive('updateAirfieldMinStackLevelsFromVatsimMetarServer')
            ->once();
        $serviceMock->shouldReceive('updateTmaMinStackLevelsFromVatsimMetarServer')
            ->once();

        $this->app->instance(MinStackLevelService::class, $serviceMock);

        Artisan::call('msl:generate');
    }

    public function testItReturnsSuccessOnCompletion()
    {
        $serviceMock = Mockery::mock(MinStackLevelService::class);
        $serviceMock->shouldReceive('updateAirfieldMinStackLevelsFromVatsimMetarServer');
        $serviceMock->shouldReceive('updateTmaMinStackLevelsFromVatsimMetarServer');
        $this->app->instance(MinStackLevelService::class, $serviceMock);
        $this->assertEquals(0, Artisan::call('msl:generate'));
    }
}
