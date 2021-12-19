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
use Illuminate\Support\Facades\DB;
use util\Traits\WithWakeCategories;

class CargoFlightPreferredArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    use WithWakeCategories;

    private CargoFlightPreferredArrivalStandAllocator $allocator;

    private Stand $cargoStand;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(CargoFlightPreferredArrivalStandAllocator::class);

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

        // Create another stand that's cargo, but the airline doesn't belong here
        Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '602',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
                'type_id' => StandType::where('key', 'CARGO')->first()->id,
            ]
        );
        Aircraft::create(
            [
                'code' => 'B744',
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 2.2,
            ]
        );

        // They want the cargo stand
        Airline::where('icao_code', 'VIR')->update(['is_cargo' => true]);
        DB::table('airline_stand')->insert(
            ['airline_id' => Airline::where('icao_code', 'VIR')->first()->id, 'stand_id' => $this->cargoStand->id]
        );
        $this->setWakeCategoryForAircraft('B744', 'H');
    }

    public function testItAllocatesAirlinePreferredCargoStandsOnly()
    {
        $allocation = $this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL'));
        $databaseAllocation = StandAssignment::where('callsign', 'VIR22F')->first();
        $this->assertEquals($databaseAllocation->stand_id, $allocation->stand_id);
        $this->assertEquals($this->cargoStand->id, $allocation->stand_id);
    }

    public function testItAllocatesCargoStandsIfFlightplanSaysCargo()
    {
        $aircraft = $this->createAircraft('VIR22F', 'EGLL');
        $aircraft->remarks = 'Some stuff RMK/CARGO Some more stuff';
        $allocation = $this->allocator->allocate($aircraft);
        $databaseAllocation = StandAssignment::where('callsign', 'VIR22F')->first();
        $this->assertEquals($databaseAllocation->stand_id, $allocation->stand_id);
        $this->assertEquals($this->cargoStand->id, $allocation->stand_id);
    }

    public function testItAllocatesCargoStandsAboveItsWeight()
    {
        $this->cargoStand->update(['wake_category_id' => WakeCategory::where('code', 'J')->first()->id]);

        $allocation = $this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL'));
        $databaseAllocation = StandAssignment::where('callsign', 'VIR22F')->first();
        $this->assertEquals($databaseAllocation->stand_id, $allocation->stand_id);
        $this->assertEquals($this->cargoStand->id, $allocation->stand_id);
    }

    public function testItReturnsNothingIfNoStandsToAllocated()
    {
        $this->cargoStand->delete();

        $this->assertNull($this->allocator->allocate($this->createAircraft('VIR22F', 'EGLL')));
        $this->assertNull(StandAssignment::where('callsign', 'VIR22F')->first());
    }

    public function testItDoesntAllocateOccupiedStands()
    {
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
        Airline::where('icao_code', 'VIR')->update(['is_cargo' => false]);
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
