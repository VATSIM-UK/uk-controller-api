<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\Stand\StandService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Mockery;

class AllocateStandForArrivalTest extends BaseFunctionalTestCase
{
    public function testItAllocatesForEachAircraft()
    {
        Config::set('stands.auto_allocate', true);
        $serviceMock = Mockery::mock(StandService::class);
        $serviceMock->shouldReceive('getAircraftEligibleForArrivalStandAllocation')
            ->once()
            ->andReturn(
                new Collection(
                    [
                        NetworkAircraft::find('BAW123'),
                        NetworkAircraft::find('BAW456')
                    ]
                )
            );

        $serviceMock->shouldReceive('allocateStandForAircraft')
            ->once()
            ->with(Mockery::on(function (NetworkAircraft $aircraft) {
                return $aircraft->callsign === 'BAW123';
            }));

        $serviceMock->shouldReceive('removeAllocationIfDestinationChanged')
            ->once()
            ->with(Mockery::on(function (NetworkAircraft $aircraft) {
                return $aircraft->callsign === 'BAW123';
            }));

        $serviceMock->shouldReceive('allocateStandForAircraft')
            ->once()
            ->with(Mockery::on(function (NetworkAircraft $aircraft) {
                return $aircraft->callsign === 'BAW456';
            }));

        $serviceMock->shouldReceive('removeAllocationIfDestinationChanged')
            ->once()
            ->with(Mockery::on(function (NetworkAircraft $aircraft) {
                return $aircraft->callsign === 'BAW456';
            }));

        $this->app->instance(StandService::class, $serviceMock);

        Artisan::call('stands:assign-arrival');
    }

    public function testItDoesntAllocateStandsIfDisabled()
    {
        Config::set('stands.auto_allocate', false);
        $serviceMock = Mockery::mock(StandService::class);
        $serviceMock->shouldNotReceive('allocateStandForAircraft');
        $this->app->instance(StandService::class, $serviceMock);

        Artisan::call('stands:assign-arrival');
    }
}
