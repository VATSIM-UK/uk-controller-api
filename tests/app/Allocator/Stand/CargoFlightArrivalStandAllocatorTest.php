<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandType;
use App\Models\Vatsim\NetworkAircraft;

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

    private function createAircraft(
        string $callsign,
        string $arrivalAirport
    ): NetworkAircraft {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'cid' => 1234,
                'planned_aircraft' => 'B744',
                'planned_aircraft_short' => 'B744',
                'planned_destairport' => $arrivalAirport,
            ]
        );
    }
}
