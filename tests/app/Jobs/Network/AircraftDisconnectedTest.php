<?php

namespace App\Jobs\Network;

use App\BaseFunctionalTestCase;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;

class AircraftDisconnectedTest extends BaseFunctionalTestCase
{
    public function testItCallsHandlers()
    {
        $this->withoutEvents();
        StandAssignment::create(['callsign' => 'BAW123', 'stand_id' => 1]);
        AircraftDisconnected::dispatchSync(NetworkAircraft::find('BAW123'));

        $this->assertFalse(StandAssignment::where('callsign', 'BAW123')->exists());
    }
}
