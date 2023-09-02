<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Stand\Stand;
use App\Models\Stand\StandType;
use App\Models\Vatsim\NetworkAircraft;

class FallbackArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var FallbackArrivalStandAllocator
     */
    private $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(FallbackArrivalStandAllocator::class);
    }

    public function testItAssignsAnAppropriatelySizedStand()
    {
        // Create a stand that can only accept an A380 and create the aircraft
        $stand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'F',
            ]
        );

        Aircraft::create(
            [
                'code' => 'A388',
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 1.0,
                'aerodrome_reference_code' => 'F',
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'A388', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($stand->id, $assignment);
    }

    public function testItAssignsInAerodromeReferenceAscendingOrder()
    {
        // Create a stand that can only accept an A380 and create the aircraft
        Aircraft::where('code', 'B738')->update(['aerodrome_reference_code' => 'B']);
        $stand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'B',
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($stand->id, $assignment);
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
                'aerodrome_reference_code' => 'F',
                'assignment_priority' => 1,
            ]
        );
        $stand->refresh();

        Aircraft::create(
            [
                'code' => 'B744',
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 1.0,
                'aerodrome_reference_code' => 'E',
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'B744', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($stand->id, $assignment);
    }

    public function testItAssignsStandsInPriorityOrder()
    {
        // Create a stand that is low priority for allocation
        $lowPriority = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55C',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'F',
                'assignment_priority' => 100,
            ]
        );

        // Create a stand that can only accept an A380 and create the aircraft
        $highPriority = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55L',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'F',
                'assignment_priority' => 1,
            ]
        );

        Aircraft::create(
            [
                'code' => 'A388',
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 1.0,
                'aerodrome_reference_code' => 'F',
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'A388', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($highPriority->id, $assignment);
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
                'aerodrome_reference_code' => 'F',
                'assignment_priority' => 1,
                'type_id' => StandType::cargo()->first()->id,
            ]
        );

        // Create a stand that can only accept an A380 and create the aircraft
        $stand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55L',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'F',
                'assignment_priority' => 1,
            ]
        );
        $stand->refresh();

        Aircraft::create(
            [
                'code' => 'A388',
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 1.0,
                'aerodrome_reference_code' => 'F',
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'A388', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($stand->id, $assignment);
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
                'aerodrome_reference_code' => 'F',
                'assignment_priority' => 1,
            ]
        );
        $stand->refresh();
        $occupier = $this->createAircraft('AEU253', 'B744', 'EGLL');
        $occupier->occupiedStand()->sync([$stand->id]);

        Aircraft::create(
            [
                'code' => 'B744',
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 1.0,
                'aerodrome_reference_code' => 'E',
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'B744', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItReturnsNothingOnNoStandAllocated()
    {
        $this->assertNull($this->allocator->allocate($this->createAircraft('BAW898', 'B738', 'XXXX')));
    }

    private function createAircraft(
        string $callsign,
        string $type,
        string $arrivalAirport
    ): NetworkAircraft {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'cid' => 1234,
                'planned_aircraft' => $type,
                'planned_aircraft_short' => $type,
                'planned_destairport' => $arrivalAirport,
                'aircraft_id' => Aircraft::where('code', $type)->first()?->id,
            ]
        );
    }
}
