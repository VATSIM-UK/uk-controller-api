<?php

namespace App\Jobs\Prenote;

use App\BaseFunctionalTestCase;
use App\Events\Prenote\PrenoteDeletedEvent;
use App\Models\Prenote\PrenoteMessage;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

class CancelOutstandingPrenoteMessagesTest extends BaseFunctionalTestCase
{
    private CancelOutstandingPrenoteMessages $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(CancelOutstandingPrenoteMessages::class);
        Carbon::setTestNow(Carbon::now());
        Event::fake();
    }

    public function testItRemovesOutstandingDepartureReleaseRequests()
    {
        $prenote1 = PrenoteMessage::factory()->create(['callsign' => 'BAW123']);
        PrenoteMessage::factory()->create(['callsign' => 'BAW456']);
        $prenote3 = PrenoteMessage::factory()->create(['callsign' => 'BAW123']);

        $this->listener->perform(NetworkAircraft::find('BAW123'));

        Event::assertDispatched(
            PrenoteDeletedEvent::class,
            function (PrenoteDeletedEvent $event) use ($prenote1) {
                return $event->broadcastWith() === ['id' => $prenote1->id];
            }
        );

        Event::assertDispatched(
            PrenoteDeletedEvent::class,
            function (PrenoteDeletedEvent $event) use ($prenote3) {
                return $event->broadcastWith() === ['id' => $prenote3->id];
            }
        );

        $this->assertSoftDeleted($prenote1);
        $this->assertSoftDeleted($prenote3);
        $this->assertNotNull(PrenoteMessage::where('callsign', 'BAW456')->first());
    }
}
