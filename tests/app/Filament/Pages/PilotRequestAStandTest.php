<?php

namespace App\Filament\Pages;

use App\BaseFilamentTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Stand\StandRequest;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Livewire\Livewire;

class PilotRequestAStandTest extends BaseFilamentTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        NetworkAircraft::find('BAW123')->update(
            [
                'cid' => self::ACTIVE_USER_CID,
                'planned_destairport' => 'EGLL',
                'aircraft_id' => 1,
                'airline_id' => 1,
            ]
        );
    }

    public function testItRenders()
    {
        Livewire::test(PilotRequestAStand::class)
            ->assertOk()
            ->assertSee('Request a Stand');
    }

    public function testItShowsTheCurrentStandRequest()
    {
        StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'callsign' => 'BAW123',
                'requested_time' => Carbon::now(),
            ]
        );
        Livewire::test(PilotRequestAStand::class)
            ->assertOk()
            ->assertSeeHtml('You have currently requested Stand <b>EGLL / 1L</b>');
    }

    public function testItShowsTheFormIfNoCurrentRequest()
    {
        StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'callsign' => 'BAW123',
                'requested_time' => Carbon::now()->subHour(),
            ]
        );
        StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::BANNED_USER_CID,
                'callsign' => 'BAW123',
                'requested_time' => Carbon::now(),
            ]
        );
        Livewire::test(PilotRequestAStand::class)
            ->assertOk()
            ->assertSeeHtml('Stand request for')
            ->assertSeeHtml('BAW123 at EGLL');
    }

    public function testItCreatesTheRequestAndReturnsToTheCurrentStandRequest()
    {
        StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'callsign' => 'BAW123',
                'requested_time' => Carbon::now()->subMinutes(20),
            ]
        );

        // Check we have the form
        $test = Livewire::test(PilotRequestAStand::class)
            ->assertOk()
            ->assertSeeHtml('Stand request for')
            ->assertSeeHtml('BAW123 at EGLL');

        // Create the request
        StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'callsign' => 'BAW123',
                'requested_time' => Carbon::now(),
            ]
        );

        // Emit the event and check we now see the status
        $test->fireEvent('requestAStandFormSubmitted');
        $test->assertSeeHtml('You have currently requested Stand <b>EGLL / 1L</b>');
    }

    public function testItRelinquishesTheStandRequestAndReturnsToFormPage()
    {
        // Create the request and check we see the current stand request
        $request = StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'callsign' => 'BAW123',
                'requested_time' => Carbon::now(),
            ]
        );
        $test = Livewire::test(PilotRequestAStand::class)
            ->assertOk()
            ->assertSeeHtml('You have currently requested Stand <b>EGLL / 1L</b>');

        // Delete the request (emitting the delete event) and check we now see the form
        $request->delete();
        $test->fireEvent('currentStandRequestRelinquished')
            ->assertSeeHtml('Stand request for')
            ->assertSeeHtml('BAW123 at EGLL');
    }
}
