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
        $this->handler = new DeleteNetworkAircraft(NetworkAircraft::find('BAW123'));
    }

    public function testItDeletesTheAircraft()
    {
        $this->handler->handle();
        $this->assertNull(NetworkAircraft::find('BAW123'));
    }
}
