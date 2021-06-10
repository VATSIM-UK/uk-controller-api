<?php

namespace App\Jobs\Release\Departure;

use App\BaseFunctionalTestCase;
use App\Events\DepartureReleaseRequestCancelledEvent;
use App\Models\Release\Departure\DepartureReleaseRequest;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

class CancelOutstandingDepartureReleaseRequestsTest extends BaseFunctionalTestCase
{
    private CancelOutstandingDepartureReleaseRequests $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(CancelOutstandingDepartureReleaseRequests::class);
        Carbon::setTestNow(Carbon::now());
        Event::fake();
    }

    public function testItRemovesOutstandingDepartureReleaseRequests()
    {
        $request1 = DepartureReleaseRequest::factory()->create(['callsign' => 'BAW123']);
        DepartureReleaseRequest::factory()->create(['callsign' => 'BAW456']);
        $request3 = DepartureReleaseRequest::factory()->create(['callsign' => 'BAW123']);

        $this->listener->perform(NetworkAircraft::find('BAW123'));

        Event::assertDispatched(
            DepartureReleaseRequestCancelledEvent::class,
            function (DepartureReleaseRequestCancelledEvent $event) use ($request1) {
                return $event->broadcastWith() === ['id' => $request1->id];
            }
        );

        Event::assertDispatched(
            DepartureReleaseRequestCancelledEvent::class,
            function (DepartureReleaseRequestCancelledEvent $event) use ($request3) {
                return $event->broadcastWith() === ['id' => $request3->id];
            }
        );

        $this->assertSoftDeleted($request1);
        $this->assertSoftDeleted($request3);
        $this->assertNotNull(DepartureReleaseRequest::where('callsign', 'BAW456')->first());
    }
}
