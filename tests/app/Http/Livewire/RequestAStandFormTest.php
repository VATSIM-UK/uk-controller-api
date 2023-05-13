<?php

namespace App\Http\Livewire;

use App\BaseFilamentTestCase;
use App\Models\Aircraft\Aircraft;
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

    public function testItDisplaysAValidationErrorIfTimeBeforeNow()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedTime', Carbon::now()->subMinute()->toDateTimeString())
            ->call('submit')
            ->assertHasErrors('requestedTime')
            ->assertOk();
    }

    public function testItDisplaysAValidationErrorIfTimeAfter24Hours()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedTime', Carbon::now()->addDay()->addMinute()->toDateTimeString())
            ->call('submit')
            ->assertHasErrors('requestedTime')
            ->assertOk();
    }

    public function testItDisplaysTheRequestTimeInformation()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedTime', Carbon::now())
            ->assertOk()
            ->assertSeeHtml(
                [
                    'Your request will expire at <b>16:00Z</b> and will be considered by',
                    'the stand allocator from <b>15:00Z</b>.',
                ]
            );
    }

    public function testItRequestsAStand()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedStand', 1)
            ->set('requestedTime', Carbon::now()->addMinute()->startOfMinute())
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
        $this->assertEquals(Carbon::now()->addMinute()->startOfMinute(), $request->requested_time);
        $this->assertNull($request->deleted_at);

        $this->assertEquals($request->id, $history->id);
        $this->assertEquals('BAW123', $history->callsign);
        $this->assertEquals(self::ACTIVE_USER_CID, $history->user_id);
        $this->assertEquals(1, $history->stand_id);
        $this->assertEquals(Carbon::now()->addMinute()->startOfMinute(), $history->requested_time);
        $this->assertNull($history->deleted_at);
    }

    public function testItEmitsAnUpdateEvent()
    {
        Livewire::test(RequestAStandForm::class)
            ->set('requestedStand', 1)
            ->set('requestedTime', Carbon::now()->addMinute())
            ->call('submit')
            ->assertHasNoErrors()
            ->assertOk()
            ->assertEmitted('requestAStandFormSubmitted');
    }
}
