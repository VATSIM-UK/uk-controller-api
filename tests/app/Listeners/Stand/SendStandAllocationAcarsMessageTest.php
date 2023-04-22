<?php

namespace App\Listeners\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;

class SendStandAllocationAcarsMessageTest extends BaseFunctionalTestCase
{
    private readonly StandAssignedEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        $airfield = Airfield::factory()->create([
            'latitude' => 51.4775,
            'longitude' => -0.461389,
        ]);
        $stand = Stand::factory()->create(['airfield_id' => $airfield->id]);
        $aircraft = NetworkAircraft::factory()->create(
            [
                'planned_destairport' => $airfield->code,
                'latitude' => 52.453889,
                'longitude' => -1.748056,
            ]
        );
        $assignment = StandAssignment::create(
            [
                'callsign' => $aircraft->callsign,
                'stand_id' => $stand->id,
            ]
        );
        $this->event = new StandAssignedEvent($assignment);
    }

    public function testItSendsAnAcarsMessage()
    {
    }
}
