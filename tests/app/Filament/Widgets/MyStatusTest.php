<?php

namespace App\Filament\Widgets;

use App\BaseFilamentTestCase;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Models\Vatsim\NetworkControllerPosition;
use Livewire\Livewire;

class MyStatusTest extends BaseFilamentTestCase
{
    public function testDisplaysNotTracked()
    {
        Livewire::test(MyStatus::class)
            ->assertSee('My Status')
            ->assertSee('Not tracked.');
    }

    public function testDisplaysUnknownController()
    {
        $position = NetworkControllerPosition::factory()->asUser($this->filamentUser())->create();

        Livewire::test(MyStatus::class)
            ->assertSee('My Status')
            ->assertSee('Logged In As:')
            ->assertSee(sprintf('%s - %s', $position->callsign, $position->frequency));
    }

    public function testDisplaysKnownController()
    {
        NetworkControllerPosition::factory()
            ->withControllerPosition(1)
            ->asUser($this->filamentUser())
            ->create();

        Livewire::test(MyStatus::class)
            ->assertSee('My Status')
            ->assertSee('Controlling As:')
            ->assertSee('EGLL_S_TWR - 118.500');
    }

    public function testDisplaysFlightToUnknownAirport()
    {
        $aircraft = NetworkAircraft::factory()
            ->asUser($this->filamentUser())
            ->create(['planned_destairport' => 'EDDM', 'planned_depairport' => 'EGLL']);

        Livewire::test(MyStatus::class)
            ->assertSee('My Status')
            ->assertSee('Flying As:')
            ->assertSee(sprintf('%s (%s - %s)', $aircraft->callsign, 'EGLL', 'EDDM'));
    }

    public function testDisplaysFlightToKnownAirportNoStandYet()
    {
        $aircraft = NetworkAircraft::factory()
            ->asUser($this->filamentUser())
            ->create(['planned_destairport' => 'EGLL', 'planned_depairport' => 'EDDM']);

        Livewire::test(MyStatus::class)
            ->assertSee('My Status')
            ->assertSee('Flying As:')
            ->assertSee(sprintf('%s (%s - %s)', $aircraft->callsign, 'EDDM', 'EGLL'))
            ->assertSee('Assigned Arrival Stand:')
            ->assertSee('--');
    }

    public function testDisplaysFlightToKnownAirportArrivalStandAssigned()
    {
        $aircraft = NetworkAircraft::factory()
            ->asUser($this->filamentUser())
            ->create(['planned_destairport' => 'EGLL', 'planned_depairport' => 'EDDM']);
        $stand = Stand::factory()->create(['airfield_id' => 1]);

        StandAssignment::create(['callsign' => $aircraft->callsign, 'stand_id' => $stand->id]);

        Livewire::test(MyStatus::class)
            ->assertSee('My Status')
            ->assertSee('Flying As:')
            ->assertSee(sprintf('%s (%s - %s)', $aircraft->callsign, 'EDDM', 'EGLL'))
            ->assertSee('Assigned Arrival Stand:')
            ->assertSee($stand->identifier);
    }

    public function testDisplaysFlightToKnownWithStandStillAssignedForDeparture()
    {
        $aircraft = NetworkAircraft::factory()
            ->asUser($this->filamentUser())
            ->create(['planned_destairport' => 'EGBB', 'planned_depairport' => 'EGLL']);
        $stand = Stand::factory()->create(['airfield_id' => 1]);
        StandAssignment::create(['callsign' => $aircraft->callsign, 'stand_id' => $stand->id]);

        Livewire::test(MyStatus::class)
            ->assertSee('My Status')
            ->assertSee('Flying As:')
            ->assertSee(sprintf('%s (%s - %s)', $aircraft->callsign, 'EGLL', 'EGBB'))
            ->assertSee('Assigned Arrival Stand:')
            ->assertSee('--');
    }
}
