<?php

namespace App\Http\Livewire;

use App\BaseFilamentTestCase;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Livewire\Livewire;

class StandStatusTest extends BaseFilamentTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        NetworkAircraft::find('BAW123')->update(['cid' => self::ACTIVE_USER_CID, 'planned_destairport' => 'EGLL']);
        Carbon::setTestNow(Carbon::now());
    }

    public function testItDisplaysStandIsOccupiedByAnotherAircraft()
    {
        Stand::find(1)->occupier()->sync(['BAW456']);
        $this->assertComponentState(
            $this->getUnavailableStandFullMessage('This stand is currently occupied by another aircraft.')
        );
    }

    public function testItDisplaysStandIsAvailableOccupiedByUser()
    {
        Stand::find(1)->occupier()->sync(['BAW123']);
        $this->assertComponentState('This stand is currently occupied by your aircraft.');
    }

    public function testItDisplaysStandIsAssignedToAnotherAircraft()
    {
        StandAssignment::create(['stand_id' => 1, 'callsign' => 'BAW456']);
        $this->assertComponentState(
            $this->getUnavailableStandFullMessage('This stand is currently assigned to another aircraft.')
        );
    }

    public function testItDisplaysStandIsAvailableAssignedToUser()
    {
        StandAssignment::create(['stand_id' => 1, 'callsign' => 'BAW123']);
        $this->assertComponentState('This stand is currently assigned to you.');
    }

    public function testItDisplaysStandIsReservedForAnotherAircraft()
    {
        StandReservation::create(
            [
                'stand_id' => 1,
                'callsign' => 'BAW456',
                'start' => Carbon::now()->subHour(),
                'end' => Carbon::now()->addHour(),
            ]
        );
        $this->assertComponentState(
            $this->getUnavailableStandFullMessage('This stand is currently reserved for another aircraft.')
        );
    }

    public function testItDisplaysStandIsAvailableReservedForUser()
    {
        StandReservation::create(
            [
                'stand_id' => 1,
                'callsign' => 'BAW123',
                'start' => Carbon::now()->subHour(),
                'end' => Carbon::now()->addHour(),
            ]
        );
        $this->assertComponentState('This stand is currently reserved for you.');
    }

    public function testItDisplaysStandIsUnavailable()
    {
        $extraStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST7',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        Stand::find(1)->pairedStands()->sync([$extraStand->id]);
        $extraStand->pairedStands()->sync([1]);
        $extraStand->occupier()->sync(['BAW456']);
        $this->assertComponentState(
            $this->getUnavailableStandFullMessage(
                'This stand is currently unavailable, it may be that another neighbouring stand is occupied, preventing this stand from being assigned.'
            )
        );
    }

    public function testItDisplaysStandIsReservedSoonForAnotherAircraft()
    {
        StandReservation::create(
            [
                'stand_id' => 1,
                'callsign' => 'BAW456',
                'start' => Carbon::now()->addMinutes(10),
                'end' => Carbon::now()->addHour(),
            ]
        );
        $this->assertComponentState(
            $this->getUnavailableStandFullMessage(
                'This stand is available, but is reserved for another aircraft at ' . Carbon::now()->addMinutes(
                    10
                )->format('H:m\Z') . '.'
            )
        );
    }

    public function testItDisplaysStandIsAvailableReservedSoonForUser()
    {
        StandReservation::create(
            [
                'stand_id' => 1,
                'callsign' => 'BAW123',
                'start' => Carbon::now()->addMinutes(10),
                'end' => Carbon::now()->addHour(),
            ]
        );
        $this->assertComponentState('This stand will soon be reserved for you.');
    }

    public function testItDisplaysStandIsAvailableRequestedOnlyByUser()
    {
        StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'callsign' => 'BAW123',
                'requested_time' => Carbon::now(),
            ]
        );
        $this->assertComponentState('This stand is currently available.');
    }

    public function testItDisplaysStandIsUnavailableRequestedByManyUsers()
    {
        StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
                'callsign' => 'BAW123',
                'requested_time' => Carbon::now(),
            ]
        );
        StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::BANNED_USER_CID,
                'callsign' => 'BAW456',
                'requested_time' => Carbon::now(),
            ]
        );
        $this->assertComponentState(
            $this->getUnavailableStandFullMessage(
                'This stand has been requested by multiple aircraft. It will be assigned on a first-come first-serve basis.'
            )
        );
    }

    public function testItDisplaysStandIsUnavailableRequestedByOtherUser()
    {
        StandRequest::create(
            [
                'stand_id' => 1,
                'user_id' => self::BANNED_USER_CID,
                'callsign' => 'BAW456',
                'requested_time' => Carbon::now(),
            ]
        );
        $this->assertComponentState(
            $this->getUnavailableStandFullMessage(
                'This stand has been requested by multiple aircraft. It will be assigned on a first-come first-served basis.'
            )
        );
    }

    public function testItDisplaysStandIsAvailable()
    {
        $this->assertComponentState('This stand is currently available.');
    }

    private function assertComponentState(string $message): void
    {
        Livewire::test(StandStatus::class, ['stand' => Stand::find(1)])
            ->assertOk()
            ->assertSeeHtml($message);
    }

    private function getUnavailableStandFullMessage(string $message): string
    {
        return sprintf(
            '%s %s',
            $message,
            'You may still request this stand, but it will not be assigned to you if it is still unavailable when your stand assignment is made.'
        );
    }
}
