<?php

namespace App\Listeners\Network;

use App\BaseUnitTestCase;
use App\Jobs\Network\IdentifyActiveControllerPositions;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Facades\Bus;
use Mockery;

class NetworkControllersUpdatedTest extends BaseUnitTestCase
{
    private NetworkControllersUpdated $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(NetworkControllersUpdated::class);
    }

    public function testItQueuesUpJobs()
    {
        $pendingChain = Mockery::mock(PendingChain::class);
        Bus::shouldReceive('chain')->with(
            [
                new IdentifyActiveControllerPositions(),
            ],
        )
            ->once()
            ->andReturn($pendingChain);
        $pendingChain->shouldReceive('dispatch')->once();
        $this->assertTrue($this->handler->handle());
    }
}
