<?php

namespace App\Listeners\Network;

use App\BaseUnitTestCase;
use App\Jobs\Stand\OccupyStands;
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
            ],
        )
            ->once()
            ->andReturn($pendingChain);
        $pendingChain->shouldReceive('dispatch')->once();
        $this->assertTrue($this->handler->handle());
    }
}
