<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandReservation;
use App\Models\Stand\StandType;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Carbon;

class CargoFlightArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    private CargoFlightArrivalStandAllocator $allocator;

    private Stand $cargoStand;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(CargoFlightArrivalStandAllocator::class);

        // Make a small cargo stand so it can be ignored by weight considerations
        Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'B',
                'type_id' => StandType::where('key', 'CARGO')->first()->id,
            ]
        );
        $this->cargoStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '601',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
                'type_id' => StandType::where('key', 'CARGO')->first()->id,
            ]
        );

        // Create another stand that's not cargo
        Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '602',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
                'type_id' => StandType::where('key', 'DOMESTIC')->first()->id,
            ]
        );
        Aircraft::create(
            [
                'code' => 'B744',
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 2.2,
                'aerodrome_reference_code' => 'E',
            ]
        );
    }

    public function testItAllocatesCargoStandsIfFlightplanSaysCargo()
    {
        $aircraft = $this->createAircraft('VIR22F', 'EGLL');
        $aircraft->remarks = 'Some stuff RMK/CARGO Some more stuff';
        $allocation = $this->allocator->allocate($aircraft);
        $this->assertEquals($this->cargoStand->id, $allocation);
    }

    public function testItAllocatesCargoStandsAboveItsWeight()
    {
        $this->cargoStand->update(['aerdrome_reference_code' => 'E']);

        $aircraft = $this->createAircraft('VIR22F', 'EGLL');
        $aircraft->remarks = 'Some stuff RMK/CARGO Some more stuff';
        $allocation = $this->allocator->allocate($aircraft);
        $this->assertEquals($this->cargoStand->id, $allocation);
    }

    public function testItReturnsNothingIfNoStandsToAllocated()
    {
        $this->cargoStand->delete();

        $aircraft = $this->createAircraft('VIR22F', 'EGLL');
        $aircraft->remarks = 'Some stuff RMK/CARGO Some more stuff';
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateOccupiedStands()
    {
        StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => $this->cargoStand->id
            ]
        );

        $aircraft = $this->createAircraft('VIR22F', 'EGLL');
        $aircraft->remarks = 'Some stuff RMK/CARGO Some more stuff';
        $allocation = $this->allocator->allocate($aircraft);
        $this->assertNull($allocation);
    }

    public function testItDoesntAllocateCargoStandsIfFlightplanNotCargo()
    {
        $aircraft = $this->createAircraft('VIR22F', 'EGLL');
        $aircraft->remarks = 'Some stuff but not CARGO Some more stuff';
        $allocation = $this->allocator->allocate($aircraft);
        $this->assertNull($allocation);
    }

    public function testItDoesntRankStandsIfUnknownAircraft()
    {
        $aircraft = $this->newAircraft('BAW1234', 'EGLL', 'C172');
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
                'wingspan' => 0.5,
                'length' => 0.6,
            ]
        );

        // Should be ranked first - its the smallest stand that's applicable
        $standA1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'A1',
                'assignment_priority' => 100,
                'aerodrome_reference_code' => 'E',
                'type_id' => 3,
            ]
        );
        StandReservation::create(
            [
                'stand_id' => $standA1->id,
                'start' => Carbon::now()->subMinutes(1),
                'end' => Carbon::now()->addMinutes(1),
            ]
        );

        // Should be ranked joint second, bigger than A1, but same priority
        $standB1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'B1',
                'assignment_priority' => 100,
                'type_id' => 3,
            ]
        );
        StandRequest::factory()->create(['requested_time' => Carbon::now(), 'stand_id' => $standB1->id]);
        $standB2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'B2',
                'assignment_priority' => 100,
                'type_id' => 3,
            ]
        );

        // Should be ranked joint third, same size as B1 and B2, but lower priority
        $standC1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C1',
                'assignment_priority' => 101,
                'type_id' => 3,
            ]
        );
        $standC2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C2',
                'assignment_priority' => 101,
                'type_id' => 3,
            ]
        );

        // Should not appear in rankings - wrong airfield
        Stand::factory()->create(['airfield_id' => 2, 'identifier' => 'D1', 'type_id' => 3]);

        // Should not appear in rankings - not cargo
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'E1',
                'type_id' => 2,
            ]
        );

        // Should not appear in rankings - too small ARC
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'F1',
                'aerodrome_reference_code' => 'A',
                'type_id' => 3,
            ]
        );

        // Should not appear in rankings - too small max aircraft size
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'G1',
                'max_aircraft_id_length' => $cessna->id,
                'max_aircraft_id_wingspan' => $cessna->id,
                'type_id' => 3,
            ]
        );

        // Should not appear in rankings - closed
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'H1',
                'aerodrome_reference_code' => 'E',
                'closed_at' => Carbon::now(),
                'type_id' => 3,
            ]
        );

        $expectedRanks = [
            $standA1->id => 1,
            $standB1->id => 2,
            $standB2->id => 2,
            $standC1->id => 3,
            $standC2->id => 3,
        ];

        $aircraft = $this->newAircraft('VIR22F', $airfield->code);
        $aircraft->remarks = 'Some stuff RMK/CARGO Some more stuff';
        $actualRanks = $this->allocator->getRankedStandAllocation(
            $aircraft
        )->mapWithKeys(
                fn($stand) => [$stand->id => $stand->rank]
            )
            ->toArray();

        $this->assertEquals($expectedRanks, $actualRanks);
    }
    private function createAircraft(
        string $callsign,
        string $arrivalAirport,
        string $aircraftType = 'B744'
    ): NetworkAircraft {
        return tap(
            $this->newAircraft($callsign, $arrivalAirport, $aircraftType),
            fn(NetworkAircraft $aircraft) => $aircraft->save()
        );
    }

    private function newAircraft(
        string $callsign,
        string $arrivalAirport,
        string $aircraftType = 'B744'
    ): NetworkAircraft {
        return new NetworkAircraft(
            [
                'callsign' => $callsign,
                'cid' => 1234,
                'planned_aircraft' => $aircraftType,
                'planned_aircraft_short' => $aircraftType,
                'planned_destairport' => $arrivalAirport,
                'airline_id' => Airline::where('icao_code', 'VIR')->first()->id,
                'aircraft_id' => Aircraft::where('code', $aircraftType)->first()?->id,
            ]
        );
    }
}
