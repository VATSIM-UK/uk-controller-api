<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\StandService;
use Illuminate\Support\Facades\Artisan;
use Mockery;

class AllocateStandForArrivalTest extends BaseFunctionalTestCase
{
    public function testItAllocatesForEachAircraft()
    {
        $serviceMock = Mockery::mock(StandService::class);
        $serviceMock->shouldReceive('allocateStandForAircraft')
            ->with(Mockery::on(function (NetworkAircraft $aircraft) {
                return $aircraft->callsign === 'BAW123';
            }));

        $serviceMock->shouldReceive('allocateStandForAircraft')
            ->with(Mockery::on(function (NetworkAircraft $aircraft) {
                return $aircraft->callsign === 'BAW456';
            }));

        $serviceMock->shouldReceive('allocateStandForAircraft')
            ->with(Mockery::on(function (NetworkAircraft $aircraft) {
                return $aircraft->callsign === 'BAW789';
            }));

        $serviceMock->shouldReceive('allocateStandForAircraft')
            ->with(Mockery::on(function (NetworkAircraft $aircraft) {
                return $aircraft->callsign === 'RYR824';
            }));

        $this->app->instance(StandService::class, $serviceMock);

        Artisan::call('stands:assign-arrival');
    }
}
