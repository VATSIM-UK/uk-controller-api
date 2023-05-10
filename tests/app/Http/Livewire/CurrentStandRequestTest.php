<?php

namespace App\Http\Livewire;

use App\BaseFilamentTestCase;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandRequestHistory;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Livewire\Livewire;

class CurrentStandRequestTest extends BaseFilamentTestCase
{
    private readonly StandRequest $standRequest;
    private readonly StandRequestHistory $history;

    public function setUp(): void
    {
        parent::setUp();
        NetworkAircraft::find('BAW123')->update(['cid' => self::ACTIVE_USER_CID, 'planned_destairport' => 'EGLL']);
        $this->standRequest = StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'callsign' => 'BAW123',
                'requested_time' => Carbon::now(),
            ]
        );

        $this->history = new StandRequestHistory($this->standRequest->toArray());
        $this->history->id = $this->standRequest->id;
        $this->history->save();
    }

    public function testItRenders()
    {
        Livewire::test(CurrentStandRequest::class, ['standRequest' => $this->standRequest])
            ->assertOk()
            ->assertSeeHtml('You have currently requested Stand <b>EGLL / 1L</b>');
    }

    public function testItShowsCurrentStandStatus()
    {
        Livewire::test(CurrentStandRequest::class, ['standRequest' => $this->standRequest])
            ->assertOk()
            ->assertSeeHtml('This stand is currently available.');
    }

    public function testItRelinquishesStandRequest()
    {
        Livewire::test(CurrentStandRequest::class, ['standRequest' => $this->standRequest])
            ->assertOk()
            ->call('relinquish', $this->standRequest->id)
            ->assertEmitted('currentStandRequestRelinquished');

        $this->standRequest->refresh();
        $this->history->refresh();
        $this->assertSoftDeleted($this->standRequest);
        $this->assertNotNull($this->history->deleted_at);
    }

    public function testItDoesntRelinquishAnotherUsersRequest()
    {
        $extraRequest = StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::BANNED_USER_CID,
                'callsign' => 'BAW123',
                'requested_time' => Carbon::now(),
            ]
        );

        Livewire::test(CurrentStandRequest::class, ['standRequest' => $this->standRequest])
            ->assertOk()
            ->call('relinquish', $extraRequest->id)
            ->assertNotEmitted('currentStandRequestRelinquished');

        $this->assertDatabaseCount('stand_requests', 2);
        $this->assertDatabaseHas('stand_requests', ['id' => $extraRequest->id, 'deleted_at' => null]);
    }
}
