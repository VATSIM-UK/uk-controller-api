<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Stand\StandRequest;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

class StandRequestServiceTest extends BaseFunctionalTestCase
{

    private readonly StandRequestService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(StandRequestService::class);
    }

    public function testItReturnsActiveRequestForAircraft()
    {
        $request = StandRequest::factory()->create(['user_id' => 1203533, 'callsign' => 'BAW123']);
        $aircraft = NetworkAircraft::find('BAW123');

        $this->assertEquals($request->id, $this->service->activeRequestForAircraft($aircraft)->id);
    }

    public function testItReturnsNullIfRequestIsForDifferentCallsign()
    {
        StandRequest::factory()->create(['user_id' => 1203533, 'callsign' => 'BAW456']);
        $aircraft = NetworkAircraft::find('BAW123');

        $this->assertNull($this->service->activeRequestForAircraft($aircraft));
    }

    public function testItReturnsNullIfRequestIsForDifferentCid()
    {
        StandRequest::factory()->create(['user_id' => 1203534, 'callsign' => 'BAW123']);
        $aircraft = NetworkAircraft::find('BAW123');

        $this->assertNull($this->service->activeRequestForAircraft($aircraft));
    }

    public function testItReturnsNullIfNoActiveRequestForAircraft()
    {
        StandRequest::factory()->create(['user_id' => 1203533, 'callsign' => 'BAW123', 'requested_time' => Carbon::now()->subMinutes(30)]);
        $aircraft = NetworkAircraft::find('BAW123');

        $this->assertNull($this->service->activeRequestForAircraft($aircraft));
    }

    public function testItReturnsNullIfNoCid()
    {
        StandRequest::factory()->create(['user_id' => 1203533, 'callsign' => 'BAW123']);
        $aircraft = NetworkAircraft::find('BAW123');
        $aircraft->cid = null;

        $this->assertNull($this->service->activeRequestForAircraft($aircraft));
    }
}
