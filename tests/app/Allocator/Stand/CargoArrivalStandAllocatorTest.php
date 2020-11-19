<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandType;
use App\Models\Vatsim\NetworkAircraft;

class CargoArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var CargoArrivalStandAllocator
     */
    private $allocator;

    /**
     * @var Stand
     */
    private $cargoStand;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(CargoArrivalStandAllocator::class);

        // Make a small cargo stand so it can be ignored by weight considerations
        Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'S')->first()->id,
                'type_id' => StandType::where('key', 'CARGO')->first()->id,
            ]
        );
        $this->cargoStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '601',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
                'type_id' => StandType::where('key', 'CARGO')->first()->id,
            ]
        );
        Aircraft::create(
            [
                'code' => 'B744',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
                'allocate_stands' => true,
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
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
                'type_id' => StandType::where('key', 'DOMESTIC')->first()->id,
            ]
        );
        Airline::where('icao_code', 'VIR')->update(['is_cargo' => true]);

        $allocation = $this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL'));
        $databaseAllocation = StandAssignment::where('callsign', 'VIR22F')->first();
        $this->assertEquals($databaseAllocation->stand_id, $allocation->stand_id);
        $this->assertEquals($this->cargoStand->id, $allocation->stand_id);
    }

    public function testItAllocatesCargoStandsAboveItsWeight()
    {
        $this->cargoStand->update(['wake_category_id' => WakeCategory::where('code', 'J')->first()->id]);
        Airline::where('icao_code', 'VIR')->update(['is_cargo' => true]);

        $allocation = $this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL'));
        $databaseAllocation = StandAssignment::where('callsign', 'VIR22F')->first();
        $this->assertEquals($databaseAllocation->stand_id, $allocation->stand_id);
        $this->assertEquals($this->cargoStand->id, $allocation->stand_id);
    }

    public function testItReturnsNothingIfNoStandsToAllocated()
    {
        $this->cargoStand->delete();
        Airline::where('icao_code', 'VIR')->update(['is_cargo' => true]);

        $this->assertNull($this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL')));
        $this->assertNull(StandAssignment::where('callsign', 'VIR22F')->first());
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
        $databaseAllocation = StandAssignment::where('callsign', 'VIR22F')->first();
        $this->assertNull($allocation);
        $this->assertNull($databaseAllocation);
    }

    public function testItDoesntAllocateCargoStandsIfAirlineNotCargo()
    {
        $allocation = $this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL'));
        $databaseAllocation = StandAssignment::where('callsign', 'VIR22F')->first();
        $this->assertNull($allocation);
        $this->assertNull($databaseAllocation);
    }

    public function testItDoesntAllocateCargoStandsIfNoAirline()
    {
        $allocation = $this->allocator->allocate($this->createAircraft('ABCDEF', 'EGLL'));
        $databaseAllocation = StandAssignment::where('callsign', 'ABCDEF')->first();
        $this->assertNull($allocation);
        $this->assertNull($databaseAllocation);
    }

    private function createAircraft(
        string $callsign,
        string $arrivalAirport
    ): NetworkAircraft {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'planned_aircraft' => 'B744',
                'planned_destairport' => $arrivalAirport,
            ]
        );
    }
}
