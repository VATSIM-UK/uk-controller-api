<?php

namespace App\Listeners\Stand;

use App\Acars\Message\Telex\StandAssignedTelexMessage;
use App\Acars\Provider\AcarsProviderInterface;
use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\User\User;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Mockery;

class SendStandAllocationAcarsMessageTest extends BaseFunctionalTestCase
{
    private readonly AcarsProviderInterface $acarsProvider;
    private readonly StandAssignedEvent $event;
    private readonly SendStandAllocationAcarsMessage $handler;

    public function setUp(): void
    {
        parent::setUp();
        // Mock acars
        $this->acarsProvider = Mockery::mock(AcarsProviderInterface::class);
        $this->app->instance(AcarsProviderInterface::class, $this->acarsProvider);

        // Ensure sending messages is turned on
        Config::set('stands.assignment_acars_message', true);

        // Create airfield and stand
        $airfield = Airfield::factory()->create([
            'latitude' => 51.4775, // Heathrow
            'longitude' => -0.461389,
        ]);
        $stand = Stand::factory()->create(['airfield_id' => $airfield->id]);

        // Create aircraft and user
        $user = User::factory()->create(['send_stand_acars_messages' => true]);
        $aircraft = NetworkAircraft::factory()->create(
            [
                'cid' => $user->id,
                'planned_destairport' => $airfield->code,
                'latitude' => 52.453889, // Over Birmingham
                'longitude' => -1.748056,
            ]
        );

        // Assign aircraft to stand
        $assignment = StandAssignment::create(
            [
                'callsign' => $aircraft->callsign,
                'stand_id' => $stand->id,
            ]
        );

        // Create handlers
        $this->event = new StandAssignedEvent($assignment);
        $this->handler = $this->app->make(SendStandAllocationAcarsMessage::class);
    }

    public function testItListensForEvents()
    {
        Event::fake();
        Event::assertListening(StandAssignedEvent::class, SendStandAllocationAcarsMessage::class);
    }

    public function testItSendsAnAcarsMessage()
    {
        $this->acarsProvider->shouldReceive('sendTelex')
            ->with(
                Mockery::on(
                    fn(StandAssignedTelexMessage $message) => $message->getTarget(
                        ) === $this->event->getStandAssignment()->callsign
                )
            )->once();

        $this->handler->handle($this->event);
    }

    public function testItDoesntSendAcarsMessageIfStandIsNotAtArrivalAirport()
    {
        $this->event->getStandAssignment()->aircraft->planned_destairport = '1234';
        $this->acarsProvider->shouldReceive('sendTelex')->never();
        $this->handler->handle($this->event);
    }

    public function testItDoesntSendAcarsMessageIfAircraftIsArrivingAtItsDepartureAirport()
    {
        $this->event->getStandAssignment()->aircraft->planned_depairport = $this->event->getStandAssignment()->aircraft->planned_destairport;
        $this->acarsProvider->shouldReceive('sendTelex')->never();
        $this->handler->handle($this->event);
    }

    public function testItDoesntSendAcarsMessageIfAircraftIsTooCloseToItsArrivalAirport()
    {
        $this->event->getStandAssignment()->aircraft->latitude = 51.4775;
        $this->event->getStandAssignment()->aircraft->longitude = -0.461389;
        $this->acarsProvider->shouldReceive('sendTelex')->never();
        $this->handler->handle($this->event);
    }

    public function testItDoesntSendAcarsMessageIfUserHasntPermittedIt()
    {
        $this->event->getStandAssignment()->aircraft->user->send_stand_acars_messages = false;
        $this->acarsProvider->shouldReceive('sendTelex')->never();
        $this->handler->handle($this->event);
    }

    public function testItDoesntSendAcarsMessageIfSystemTurnedOff()
    {
        Config::set('stands.assignment_acars_message', false);
        $this->acarsProvider->shouldReceive('sendTelex')->never();
        $this->handler->handle($this->event);
    }
}
