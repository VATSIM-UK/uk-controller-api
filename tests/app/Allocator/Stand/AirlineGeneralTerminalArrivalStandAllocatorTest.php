<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AirlineGeneralTerminalArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    private readonly AirlineGeneralTerminalArrivalStandAllocator $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(AirlineGeneralTerminalArrivalStandAllocator::class);
        Airline::where('icao_code', 'BAW')->first()->terminals()->attach(2);
        Stand::find(1)->update(['terminal_id' => 1]);
        Stand::find(2)->update(['terminal_id' => 2]);
        Airline::factory()->create(['icao_code' => 'EZY']);
    }

    public function testItAllocatesAStandAtTheRightTerminal()
    {
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals(2, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAssignTerminalsWithSpecificDestinations()
    {
        Stand::query()->update(['terminal_id' => null]);
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1A']);
        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'destination' => 'EGFF',
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAssignTerminalsWithSpecificCallsignSlugs()
    {
        Stand::query()->update(['terminal_id' => null]);
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1A']);
        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'callsign_slug' => '333',
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItAssignsStandsWithSpecificFullCallsigns()
    {
        Stand::query()->update(['terminal_id' => null]);
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1A']);
        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'full_callsign' => '333',
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAssignTerminalsWithSpecificAircraftTypes()
    {
        Stand::query()->update(['terminal_id' => null]);
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1A']);
        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'aircraft_id' => 1,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }


    public function testItAPrefersStandsWithNoSpecificCallsignSlugs()
    {
        Stand::query()->update(['terminal_id' => null]);
        $terminal1 = Terminal::factory()->create(['airfield_id' => 1]);
        $stand1 = Stand::factory()->withTerminal($terminal1)->create(['airfield_id' => 1, 'identifier' => '1B']);
        $terminal2 = Terminal::factory()->create(['airfield_id' => 1]);
        Stand::factory()->withTerminal($terminal2)->create(['airfield_id' => 1, 'identifier' => '1A']);

        DB::table('airline_terminal')->insert(
            [
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal2->id,
                    'callsign_slug' => '333',
                ],
                [
                    'airline_id' => 1,
                    'terminal_id' => $terminal1->id,
                    'callsign_slug' => null,
                ],
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($stand1->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesStandsInAerodromeReferenceAscendingOrder()
    {
        Aircraft::where('code', 'B738')->update(['aerodrome_reference_code' => 'B']);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'B',
                'terminal_id' => 2,
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesStandsAtAppropriateReferenceCode()
    {
        Aircraft::where('code', 'B738')->update(['aerodrome_reference_code' => 'E']);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'terminal_id' => 2,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateOccupiedStands()
    {
        $extraStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
                'terminal_id' => 2,
            ]
        );

        $occupier = $this->createAircraft('EZY7823', 'EGLL');
        $occupier->occupiedStand()->sync([2]);
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');

        $this->assertEquals($extraStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateUnavailableStands()
    {
        $extraStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
                'terminal_id' => 2,
            ]
        );
        NetworkAircraft::find('BAW123')->occupiedStand()->sync([2]);

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($extraStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateNonExistentAirlines()
    {
        $aircraft = $this->createAircraft('***1234', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItReturnsNullOnNoStandAllocated()
    {
        Stand::all()->each(function (Stand $stand)
        {
            $stand->delete();
        });
        $aircraft = $this->createAircraft('BAW999', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntRankStandsIfUnknownAircraftType()
    {
        $aircraft = $this->newAircraft('BAW23451', 'EGLL', 'EGGD', aircraftType: 'XXX');
        $this->assertEquals(collect(), $this->allocator->getRankedStandAllocation($aircraft));
    }

    public function testItDoesntRankStandsIfUnknownAirline()
    {
        $aircraft = $this->newAircraft('***1234', 'EGLL', 'EGGD');
        $this->assertEquals(collect(), $this->allocator->getRankedStandAllocation($aircraft));
    }

    public function testItGetsRankedStandAllocation()
    {
        // Create an airfield that we dont have so we know its a clean test
        $airfield = Airfield::factory()->create(['code' => 'EXXX']);
        $airfieldId = $airfield->id;

        // Create a small aircraft type to test stand size ranking
        $cessna = Aircraft::create(
            [
                'code' => 'C172',
                'allocate_stands' => true,
                'aerodrome_reference_code' => 'A',
                'wingspan' => 1,
                'length' => 12,
            ]
        );

        // Should be ranked first - it has the highest priority. Both stands on the terminal should be
        // included. Stand A1 gets a reservation and a request so that we show its not considered.
        $terminalA1 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalA1->airlines()->sync([1 => ['priority' => 100]]);
        $standA1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'terminal_id' => $terminalA1->id,
                'identifier' => 'A1',
            ]
        );
        $standA2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'terminal_id' => $terminalA1->id,
                'identifier' => 'A2',
            ]
        );
        StandReservation::create(
            [
                'stand_id' => $standA1->id,
                'start' => Carbon::now()->subMinutes(1),
                'end' => Carbon::now()->addMinutes(1),
            ]
        );
        StandRequest::factory()->create(['requested_time' => Carbon::now(), 'stand_id' => $standA1->id]);

        // Should be ranked joint second, lower priority than A1.
        $terminalB1 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalB1->airlines()->sync([1 => ['priority' => 101]]);
        $standB1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'terminal_id' => $terminalB1->id,
                'identifier' => 'B1',
                'aerodrome_reference_code' => 'C'
            ]
        );

        $terminalB2 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalB2->airlines()->sync([1 => ['priority' => 101]]);
        $standB2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'terminal_id' => $terminalB2->id,
                'identifier' => 'B2',
                'aerodrome_reference_code' => 'C'
            ]
        );

        // Should be ranked joint third, same priority as B1 and B2 but smaller stands
        $terminalC1 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalC1->airlines()->sync([1 => ['priority' => 101]]);
        $standC1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C1',
                'terminal_id' => $terminalC1->id,
            ]
        );

        $terminalC2 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalC2->airlines()->sync([1 => ['priority' => 101]]);
        $standC2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C2',
                'terminal_id' => $terminalC2->id,
            ]
        );

        // Should not appear in rankings - wrong airfield
        Terminal::find(1)->airlines()->sync([1 => ['priority' => 101]]);
        Stand::factory()->create(
            [
                'airfield_id' => 1,
                'identifier' => 'D1',
                'terminal_id' => 1
            ]
        );

        // Should not appear in rankings - wrong terminal
        $terminalD2 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'D1',
                'terminal_id' => $terminalD2->id
            ]
        );

        // Should not appear in rankings - has a full callsign
        $terminalE1 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalE1->airlines()->sync([1 => ['full_callsign' => 'xxxx']]);
        Stand::factory()->create(['airfield_id' => $airfieldId, 'identifier' => 'E1']);

        // Should not appear in rankings - has a callsign_slug
        $terminalE2 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalE2->airlines()->sync([1 => ['callsign_slug' => 'xxxx']]);
        Stand::factory()->create(['airfield_id' => $airfieldId, 'identifier' => 'E2']);

        // Should not appear in rankings - has a specific aircraft_type
        $terminalE3 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalE3->airlines()->sync([1 => ['aircraft_id' => 1]]);
        Stand::factory()->create(['airfield_id' => $airfieldId, 'identifier' => 'E3']);

        // Should not appear in rankings - has a specific destination
        $terminalE4 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalE4->airlines()->sync([1 => ['destination' => 'ABC']]);
        Stand::factory()->create(['airfield_id' => $airfieldId, 'identifier' => 'E4']);

        // Should not appear in rankings - too small ARC
        $terminalF1 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalF1->airlines()->sync([1 => ['full_callsign' => '23451']]);
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'F1',
                'aerodrome_reference_code' => 'A'
            ]
        );

        // Should not appear in rankings - too small max aircraft size
        $terminalG1 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalG1->airlines()->sync([1 => ['full_callsign' => '23451']]);
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'G1',
                'max_aircraft_length' => $cessna->length,
                'max_aircraft_wingspan' => $cessna->wingspan,
            ]
        );


        // Should not appear in rankings - closed
        $terminalH1 = Terminal::factory()->create(['airfield_id' => $airfieldId]);
        $terminalH1->airlines()->sync([1 => ['full_callsign' => '23451']]);
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'H1',
                'closed_at' => Carbon::now()
            ]
        );


        $expectedRanks = [
            $standA1->id => 1,
            $standA2->id => 1,
            $standB1->id => 2,
            $standB2->id => 2,
            $standC1->id => 3,
            $standC2->id => 3,
        ];

        $actualRanks = $this->allocator->getRankedStandAllocation(
            $this->newAircraft('BAW23451', $airfield->code, 'EGGD')
        )->mapWithKeys(
                fn($stand) => [$stand->id => $stand->rank]
            )
            ->toArray();

        $this->assertEquals($expectedRanks, $actualRanks);
    }

    private function createAircraft(
        string $callsign,
        string $arrivalAirport,
        string $departureAirport = 'EGGD',
        string $aircraftType = 'B738'
    ): NetworkAircraft {
        return tap(
            $this->newAircraft($callsign, $arrivalAirport, $departureAirport, $aircraftType),
            fn(NetworkAircraft $aircraft) => $aircraft->save()
        );
    }

    private function newAircraft(
        string $callsign,
        string $arrivalAirport,
        string $departureAirport,
        string $aircraftType = 'B738'
    ): NetworkAircraft {
        return new NetworkAircraft(
            [
                'callsign' => $callsign,
                'cid' => 1234,
                'planned_aircraft' => $aircraftType,
                'planned_aircraft_short' => $aircraftType,
                'planned_destairport' => $arrivalAirport,
                'planned_depairport' => $departureAirport,
                'aircraft_id' => $aircraftType === 'B738' ? 1 : null,
                'airline_id' => match ($callsign) {
                    'BAW23451' => 1,
                    'EZY7823' => Airline::where('icao_code', 'EZY')->first()->id,
                    default => null,
                },
            ]
        );
    }
}
