<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandType;
use App\Models\Vatsim\NetworkAircraft;

class CargoAirlineFallbackStandAllocatorTest extends BaseFunctionalTestCase
{
    private CargoAirlineFallbackStandAllocator $allocator;

    private Stand $cargoStand;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(CargoAirlineFallbackStandAllocator::class);

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

    public function testItAllocatesCargoStandsOnly()
    {
        // Create a non-cargo stand
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
        Airline::where('icao_code', 'VIR')->update(['is_cargo' => true]);

        $allocation = $this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL'));
        $this->assertEquals($this->cargoStand->id, $allocation);
    }

    public function testItAllocatesCargoStandsAboveItsWeight()
    {
        $this->cargoStand->update(['aerodrome_reference_code' => 'F']);
        Airline::where('icao_code', 'VIR')->update(['is_cargo' => true]);

        $allocation = $this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL'));
        $this->assertEquals($this->cargoStand->id, $allocation);
    }

    public function testItReturnsNothingIfNoStandsToAllocated()
    {
        $this->cargoStand->delete();
        Airline::where('icao_code', 'VIR')->update(['is_cargo' => true]);

        $this->assertNull($this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL')));
    }

    public function testItDoesntAllocateOccupiedStands()
    {
        Airline::where('icao_code', 'VIR')->update(['is_cargo' => true]);
        StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => $this->cargoStand->id
            ]
        );

        $allocation = $this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL'));
        $this->assertNull($allocation);
    }

    public function testItDoesntAllocateCargoStandsIfAirlineNotCargo()
    {
        $allocation = $this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL'));
        $this->assertNull($allocation);
    }

    public function testItDoesntAllocateCargoStandsIfNoAirline()
    {
        $allocation = $this->allocator->allocate($this->createAircraft('ABCDEF', 'EGLL'));
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
