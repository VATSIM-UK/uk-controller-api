<?php

namespace App\Listeners\Network;

use App\BaseFunctionalTestCase;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Jobs\Hold\UnassignHoldOnDisconnect;
use App\Jobs\Network\DeleteNetworkAircraft;
use App\Jobs\Squawk\MarkAssignmentDeletedOnDisconnect;
use App\Jobs\Stand\TriggerUnassignmentOnDisconnect;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Facades\Bus;
use Mockery;

class AircraftDisconnectedTest extends BaseFunctionalTestCase
{
    private NetworkAircraft $aircraft;
    private AircraftDisconnected $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->aircraft = new NetworkAircraft(['callsign' => 'BAW123']);
        $this->handler = $this->app->make(AircraftDisconnected::class);
    }

    public function testItQueuesUpJobs()
    {
        $pendingChain = Mockery::mock(PendingChain::class);
        Bus::shouldReceive('chain')->with(
            [
                new TriggerUnassignmentOnDisconnect($this->aircraft),
                new UnassignHoldOnDisconnect($this->aircraft),
                new MarkAssignmentDeletedOnDisconnect($this->aircraft),
                new DeleteNetworkAircraft($this->aircraft),
            ],
        )
            ->once()
            ->andReturn($pendingChain);

        $pendingChain->shouldReceive('dispatch')->once();
        $this->handler->handle(new NetworkAircraftDisconnectedEvent($this->aircraft));
    }
}
