<?php

namespace App\Jobs\Network;

use App\BaseFunctionalTestCase;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\Event;

class AircraftDisconnectedTest extends BaseFunctionalTestCase
{
    public function testItCallsHandlers()
    {
        Event::fake();
        StandAssignment::create(['callsign' => 'BAW123', 'stand_id' => 1]);
        AircraftDisconnected::dispatchSync(NetworkAircraft::find('BAW123'));

        $this->assertFalse(StandAssignment::where('callsign', 'BAW123')->exists());
    }
}
