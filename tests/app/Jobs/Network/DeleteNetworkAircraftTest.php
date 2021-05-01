<?php

namespace App\Jobs\Network;

use App\BaseFunctionalTestCase;
use App\Models\Vatsim\NetworkAircraft;

class DeleteNetworkAircraftTest extends BaseFunctionalTestCase
{
    private DeleteNetworkAircraft $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(DeleteNetworkAircraft::class);
    }

    public function testItDeletesTheAircraft()
    {
        $this->handler->perform(NetworkAircraft::find('BAW123'));
        $this->assertNull(NetworkAircraft::find('BAW123'));
    }
}
