<?php

namespace App\Listeners\Network;

use App\BaseUnitTestCase;
use App\Jobs\Hold\DetectProximityToHolds;
use App\Jobs\Hold\RemoveAssignmentsForAircraftLeavingHold;
use App\Jobs\Prenote\CancelMessagesForDepartedAircraft;
use App\Jobs\Release\Departure\CancelRequestsForDepartedAircraft;
use App\Jobs\Squawk\ReserveActiveSquawks;
use App\Jobs\Stand\AssignStandsForArrival;
use App\Jobs\Stand\AssignStandsForDeparture;
use App\Jobs\Stand\OccupyStands;
use App\Jobs\Stand\RemoveDisconnectedArrivalStands;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Facades\Bus;
use Mockery;

class NetworkDataUpdatedTest extends BaseUnitTestCase
{
    private NetworkDataUpdated $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(NetworkDataUpdated::class);
    }

    public function testItQueuesUpJobs()
    {
        $pendingChain = Mockery::mock(PendingChain::class);
        Bus::shouldReceive('chain')->with(
            [
                new OccupyStands(),
                new AssignStandsForDeparture(),
                new RemoveDisconnectedArrivalStands(),
                new AssignStandsForArrival(),
                new ReserveActiveSquawks(),
                new RemoveAssignmentsForAircraftLeavingHold(),
                new CancelRequestsForDepartedAircraft(),
                new CancelMessagesForDepartedAircraft(),
                new DetectProximityToHolds(),
            ],
        )
            ->once()
            ->andReturn($pendingChain);
        $pendingChain->shouldReceive('dispatch')->once();
        $this->assertTrue($this->handler->handle());
    }
}
