<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandType;
use App\Models\Vatsim\NetworkAircraft;

class GeneralUseArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var GeneralUseArrivalStandAllocator
     */
    private $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(GeneralUseArrivalStandAllocator::class);
    }

    public function testItAssignsAnAppropriatelySizedStand()
    {
        // Create a stand that can only accept an A380 and create the aircraft
        $stand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55L',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'general_use' => true,
            ]
        );
        $stand->refresh();

        Aircraft::create(
            [
                'code' => 'A388',
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 1.0,
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'A388', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);
        $expectedAssignment = StandAssignment::where('callsign', 'AEU252')->first();

        $this->assertEquals($expectedAssignment->stand_id, $assignment->stand_id);
        $this->assertEquals($expectedAssignment->callsign, $assignment->callsign);
        $this->assertEquals($stand->id, $assignment->stand_id);
        $this->assertEquals('AEU252', $assignment->callsign);
    }

    public function testItAssignsInWeightAscendingOrder()
    {
        // Create a stand that can only accept an A380 and create the aircraft
        $stand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55L',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'S')->first()->id,
                'general_use' => true,
            ]
        );
        $stand->refresh();

        Aircraft::where('code', 'B738')->update(['wake_category_id' => WakeCategory::where('code', 'S')->first()->id]);
        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);
        $expectedAssignment = StandAssignment::where('callsign', 'AEU252')->first();

        $this->assertEquals($expectedAssignment->stand_id, $assignment->stand_id);
        $this->assertEquals($expectedAssignment->callsign, $assignment->callsign);
        $this->assertEquals($stand->id, $assignment->stand_id);
        $this->assertEquals('AEU252', $assignment->callsign);
    }

    public function testItAllocatesAboveItsWeight()
    {
        // Create a stand that can only accept an A380 and create the aircraft
        $stand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55L',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'general_use' => true,
            ]
        );
        $stand->refresh();

        Aircraft::create(
            [
                'code' => 'B744',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 1.0,
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'B744', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);
        $expectedAssignment = StandAssignment::where('callsign', 'AEU252')->first();

        $this->assertEquals($expectedAssignment->stand_id, $assignment->stand_id);
        $this->assertEquals($expectedAssignment->callsign, $assignment->callsign);
        $this->assertEquals('AEU252', $assignment->callsign);
    }

    public function testItOnlyAssignsGeneralUseStand()
    {
        // Create a stand that isn't for general allocation
        Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55C',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'general_use' => false,
            ]
        );

        // Create a stand that can only accept an A380 and create the aircraft
        $stand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55L',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'general_use' => true,
            ]
        );
        $stand->refresh();

        Aircraft::create(
            [
                'code' => 'A388',
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 1.0,
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'A388', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);
        $expectedAssignment = StandAssignment::where('callsign', 'AEU252')->first();

        $this->assertEquals($expectedAssignment->stand_id, $assignment->stand_id);
        $this->assertEquals($expectedAssignment->callsign, $assignment->callsign);
        $this->assertEquals('AEU252', $assignment->callsign);
    }

    public function testItOnlyAssignsNonCargoStands()
    {
        // Create a stand a cargo stand
        Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55C',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'general_use' => true,
                'type_id' => StandType::cargo()->first()->id,
                'general_use' => true,
            ]
        );

        // Create a stand that can only accept an A380 and create the aircraft
        $stand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55L',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'general_use' => true,
            ]
        );
        $stand->refresh();

        Aircraft::create(
            [
                'code' => 'A388',
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 1.0,
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'A388', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);
        $expectedAssignment = StandAssignment::where('callsign', 'AEU252')->first();

        $this->assertEquals($expectedAssignment->stand_id, $assignment->stand_id);
        $this->assertEquals($expectedAssignment->callsign, $assignment->callsign);
        $this->assertEquals('AEU252', $assignment->callsign);
    }

    public function testItDoesntAllocateTakenStands()
    {
        // Create a stand that can only accept an A380 and create the aircraft
        $stand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55L',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'general_use' => true,
            ]
        );
        $stand->refresh();
        $occupier = $this->createAircraft('AEU253', 'B744', 'EGLL');
        $occupier->occupiedStand()->sync([$stand->id]);

        Aircraft::create(
            [
                'code' => 'B744',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 1.0,
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'B744', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItReturnsNothingOnNoStandAllocated()
    {
        $this->assertNull($this->allocator->allocate($this->createAircraft('BAW898', 'B738', 'XXXX', true)));
        $this->assertFalse(StandAssignment::where('callsign', 'BAW898')->exists());
    }

    private function createAircraft(
        string $callsign,
        string $type,
        string $arrivalAirport
    ): NetworkAircraft {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'planned_aircraft' => $type,
                'planned_destairport' => $arrivalAirport,
            ]
        );
    }
}
