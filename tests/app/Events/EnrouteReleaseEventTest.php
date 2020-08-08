<?php

namespace App\Events;

use App\BaseFunctionalTestCase;
use App\Models\Release\Enroute\EnrouteRelease;
use Illuminate\Support\Facades\Auth;
use TestingUtils\Traits\WithSeedUsers;

class EnrouteReleaseEventTest extends BaseFunctionalTestCase
{
    use WithSeedUsers;

    /**
     * @var EnrouteRelease
     */
    private $release;

    /**
     * @var EnrouteReleaseEvent
     */
    private $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->activeUser());
        $this->release = EnrouteRelease::create(
            [
                'callsign' => 'BAW123',
                'enroute_release_type_id' => 1,
                'initiating_controller' => 'LON_C_CTR',
                'target_controller' => 'LON_S_CTR',
                'release_point' => 'ABTUM-10',
                'user_id' => Auth::user()->id,
            ]
        );
        $this->event = new EnrouteReleaseEvent($this->release);
    }

    public function testItBroadcastsToCorrectChannel()
    {
        $this->assertEquals('enroute-releases', $this->event->broadcastOn());
    }

    public function testItBroadcastsWithData()
    {
        $this->assertEquals(
            [
                'callsign' => $this->release->callsign,
                'type' => $this->release->enroute_release_type_id,
                'release_point' => $this->release->release_point,
                'initiating_controller' => $this->release->initiating_controller,
                'target_controller' => $this->release->target_controller,
            ],
            $this->event->broadcastWith()
        );
    }
}
