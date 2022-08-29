<?php

namespace App\Filament\Widgets;

use App\BaseFilamentTestCase;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

class ArrivalsBoardTest extends BaseFilamentTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('network_aircraft')->delete();
    }

    public function testItRendersArrivalsBoardWithNoStand()
    {
        $aircraft = NetworkAircraft::factory()->create(['planned_destairport' => 'EGLL']);

        Livewire::test(ArrivalsBoard::class)
            ->assertCountTableRecords(1)
            ->assertCanSeeTableRecords([$aircraft])
            ->assertSee($aircraft->callsign)
            ->assertSee('EGLL')
            ->assertSee('--');
    }

    public function testItRendersArrivalsBoardWithStand()
    {
        $aircraft = NetworkAircraft::factory()->create(['planned_destairport' => 'EGLL']);
        $stand = Stand::factory()->create(['airfield_id' => 1]);
        $aircraft->assignedStand()->save(new StandAssignment(['stand_id' => $stand->id]));

        Livewire::test(ArrivalsBoard::class)
            ->assertCountTableRecords(1)
            ->assertCanSeeTableRecords([$aircraft])
            ->assertSee($aircraft->callsign)
            ->assertSee('EGLL')
            ->assertSee($stand->identifier);
    }

    public function testItRendersArrivalsBoardWithStandAtDifferentAirfield()
    {
        $aircraft = NetworkAircraft::factory()->create(['planned_destairport' => 'EGLL']);
        $stand = Stand::factory()->create(['airfield_id' => 2]);
        $aircraft->assignedStand()->save(new StandAssignment(['stand_id' => $stand->id]));

        Livewire::test(ArrivalsBoard::class)
            ->assertCountTableRecords(0);
    }

    public function testItRendersArrivalsBoardWithUnknownArrivalAirfield()
    {
        NetworkAircraft::factory()->create(['planned_destairport' => 'EGXX']);

        Livewire::test(ArrivalsBoard::class)
            ->assertCountTableRecords(0);
    }
}
