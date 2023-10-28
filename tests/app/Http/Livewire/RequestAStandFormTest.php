<?php

namespace App\Http\Livewire;

use App\BaseFilamentTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandRequestHistory;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Livewire\Livewire;

class RequestAStandFormTest extends BaseFilamentTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        NetworkAircraft::find('BAW123')->update(
            [
                'cid' => self::ACTIVE_USER_CID,
                'planned_destairport' => 'EGLL',
            ]
        );
        Carbon::setTestNow(Carbon::now()->setHour(15)->setMinute(40));
    }

    public function testItRendersNoUserAircraft()
    {
        NetworkAircraft::find('BAW123')->delete();
        Livewire::test(RequestAStandForm::class)
            ->assertOk()
            ->assertSeeHtml('You must be flying on the VATSIM network to be able to request a stand.');
    }

    public function testItRendersUserHasUnknownAircraftType()
    {
        NetworkAircraft::find('BAW123')->update(['planned_aircraft' => 'X', 'planned_aircraft_short' => 'X']);
        Livewire::test(RequestAStandForm::class)
            ->assertOk()
            ->assertSeeHtml('Stands cannot be automatically assigned to your aircraft type.');
    }

    public function testItRendersUserAircraftCannotAllocate()
    {
        Aircraft::where('code', 'B738')->update(['allocate_stands' => false]);
        Livewire::test(RequestAStandForm::class)
            ->assertOk()
            ->assertSeeHtml('Stands cannot be automatically assigned to your aircraft type.');
    }

    public function testItRendersNoStands()
    {
        NetworkAircraft::find('BAW123')->update(['planned_destairport' => 'XXXY']);
        Livewire::test(RequestAStandForm::class)
            ->assertOk()
            ->assertSeeHtml('There are no stands available for assignment at your destination airfield.');
    }

    public function testItRenders()
    {
        Livewire::test(RequestAStandForm::class)
            ->assertOk()
            ->assertSeeHtml('Stand request for')
            ->assertSeeHtml('BAW123 at EGLL');
    }

    public function testItDisplaysTheStandStatusWhenAStandIsSelected()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedStand', 1)
            ->assertOk()
            ->assertSeeHtml('This stand is currently available.');
    }

    public function testItDisplaysAValidationErrorIfNoStandSelected()
    {
        Livewire::test(RequestAStandForm::class)
            ->call('submit')
            ->assertOk()
            ->assertHasErrors('requestedStand');
    }

    public function testItDisplaysAValidationErrorIfNoTimeRequested()
    {
        Livewire::test(RequestAStandForm::class)
            ->call('submit')
            ->assertOk()
            ->assertHasErrors('requestedTime');
    }

    public function testItDisplaysAValidationErrorIfNonNumericTimeEntered()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedTime', 'abc')
            ->call('submit')
            ->assertHasErrors('requestedTime')
            ->assertOk();
    }

    public function testItDisplaysAValidationErrorIfTimeBeforeNow()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedTime', Carbon::now()->subMinute()->format('Hi'))
            ->call('submit')
            ->assertHasErrors('requestedTime')
            ->assertOk();
    }

    public function testItDisplaysAValidationErrorIfTimeGreaterThan12HoursInAdvance()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedTime', Carbon::now()->addHours(12)->addMinute()->format('Hi'))
            ->call('submit')
            ->assertHasErrors('requestedTime')
            ->assertOk();
    }

    public function testItDisplaysTheRequestTimeInformation()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedTime', Carbon::now()->format('Hi'))
            ->assertOk()
            ->assertSeeHtml(
                [
                    'Your request will expire at <b>16:00Z</b> and will be considered by',
                    'the stand allocator from <b>15:00Z</b>.',
                ]
            );
    }

    public function testItDisplaysValidTimeInformation()
    {
        Livewire::test(RequestAStandForm::class)
            ->assertOk()
            ->assertSeeHtml(
                [
                    'Stands may be requested up to 12 hours in advance. Please enter a time between 1540 and 0340.',
                ]
            );
    }

    public function testItRequestsAStand()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedStand', 1)
            ->set('requestedTime', '1541')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertOk();

        $this->assertDatabaseCount('stand_requests', 1);
        $this->assertDatabaseCount('stand_request_history', 1);

        $request = StandRequest::latest()->firstOrFail();
        $history = StandRequestHistory::latest()->firstOrFail();

        $this->assertEquals('BAW123', $request->callsign);
        $this->assertEquals(self::ACTIVE_USER_CID, $request->user_id);
        $this->assertEquals(1, $request->stand_id);
        $this->assertEquals(Carbon::now()->setMinutes(41)->startOfMinute(), $request->requested_time);
        $this->assertNull($request->deleted_at);

        $this->assertEquals($request->id, $history->id);
        $this->assertEquals('BAW123', $history->callsign);
        $this->assertEquals(self::ACTIVE_USER_CID, $history->user_id);
        $this->assertEquals(1, $history->stand_id);
        $this->assertEquals(Carbon::now()->setMinutes(41)->startOfMinute(), $history->requested_time);
        $this->assertNull($history->deleted_at);
    }

    public function testItRequestsAStandTomorrow()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedStand', 1)
            ->set('requestedTime', '0240')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertOk();

        $this->assertDatabaseCount('stand_requests', 1);
        $this->assertDatabaseCount('stand_request_history', 1);

        $request = StandRequest::latest()->firstOrFail();
        $history = StandRequestHistory::latest()->firstOrFail();

        $this->assertEquals('BAW123', $request->callsign);
        $this->assertEquals(self::ACTIVE_USER_CID, $request->user_id);
        $this->assertEquals(1, $request->stand_id);
        $this->assertEquals(
            Carbon::now()->addDay()->setHour(2)->setMinutes(40)->startOfMinute(),
            $request->requested_time
        );
        $this->assertNull($request->deleted_at);

        $this->assertEquals($request->id, $history->id);
        $this->assertEquals('BAW123', $history->callsign);
        $this->assertEquals(self::ACTIVE_USER_CID, $history->user_id);
        $this->assertEquals(1, $history->stand_id);
        $this->assertEquals(
            Carbon::now()->addDay()->setHour(2)->setMinutes(40)->startOfMinute(),
            $history->requested_time
        );
        $this->assertNull($history->deleted_at);
    }

    public function testItEmitsAnUpdateEvent()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedStand', 1)
            ->set('requestedTime', '1940')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertOk()
            ->assertEmitted('requestAStandFormSubmitted');
    }

    public function testItRendersWarningIfExistingAssignmentAtAirfield()
    {
        StandAssignment::create(['callsign' => 'BAW123', 'stand_id' => 1]);
        Livewire::test(RequestAStandForm::class)
            ->assertOk()
            ->assertSeeHtml(
                'You cannot request a stand, as you already have a stand assigned. ' .
                    'Stands are assigned approximately 20 minutes prior to arrival.'
            );
    }

    public function testItRendersFormIfExistingAssignmentAtAnotherAirfield()
    {
        StandAssignment::create(['callsign' => 'BAW123', 'stand_id' => 3]);
        Livewire::test(RequestAStandForm::class)
            ->assertOk()
            ->assertSeeHtml('Request Stand');
    }
}
