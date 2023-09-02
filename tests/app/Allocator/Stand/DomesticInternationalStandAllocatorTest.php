<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandType;
use App\Models\Vatsim\NetworkAircraft;

class DomesticInternationalStandAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var DomesticInternationalStandAllocator
     */
    private $allocator;

    /**
     * @var Stand
     */
    private $domesticStand;

    /**
     * @var Stand
     */
    private $internationalStand;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(DomesticInternationalStandAllocator::class);
        $this->domesticStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55L',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
                'type_id' => StandType::domestic()->first()->id,
                'assignment_priority' => 1,
            ]
        );

        $this->internationalStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55R',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
                'type_id' => StandType::international()->first()->id,
                'assignment_priority' => 1,
            ]
        );
    }

    public function testItAssignsADomesticStand()
    {
        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($this->domesticStand->id, $assignment);
    }

    public function testItAssignsADomesticStandForIreland()
    {
        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL', 'EIKN');
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($this->domesticStand->id, $assignment);
    }

    public function testItAssignsAnInternationalStand()
    {
        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL', 'KJFK');
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($this->internationalStand->id, $assignment);
    }

    public function testItAssignsAerodromeReferenceCodeAppropriateStands()
    {
        Aircraft::where('code', 'B738')->update(['aerodrome_reference_code' => 'F']);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'F',
                'type_id' => StandType::domestic()->first()->id,
            ]
        );
        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($weightAppropriateStand->id, $assignment);
    }

    public function testItAssignsInAerodromeReferenceAscendingOrder()
    {
        Aircraft::where('code', 'B738')->update(['aerodrome_reference_code' => 'B']);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'type_id' => StandType::domestic()->first()->id,
                'aerodrome_reference_code' => 'B',
            ]
        );
        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($weightAppropriateStand->id, $assignment);
    }

    public function testItPrefersHigherPriorityUseStands()
    {
        // Create a stand that isn't for general allocation
        Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55C',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'F',
                'assignment_priority' => 2,
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($this->domesticStand->id, $assignment);
    }

    public function testItDoesntAllocateTakenStands()
    {
        // Create a stand that can only accept an A380 and create the aircraft
        $extraStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55C',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'F',
                'type_id' => StandType::domestic()->first()->id,
                'assignment_priority' => 1,
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL');
        StandAssignment::create(
            [
                'callsign' => 'AEU252',
                'stand_id' => $this->domesticStand->id,
            ]
        );

        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($extraStand->id, $assignment);
    }

    public function testItReturnsNothingOnNoDestinationAirport()
    {
        $aircraft = $this->createAircraft('BAW898', 'B738', '');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItReturnsNothingOnNoStandAllocated()
    {
        $this->assertNull($this->allocator->allocate($this->createAircraft('BAW898', 'B738', 'XXXX')));
    }

    private function createAircraft(
        string $callsign,
        string $type,
        string $arrivalAirport,
        string $departureAirport = 'EGKK'
    ): NetworkAircraft {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'cid' => 1234,
                'planned_aircraft' => $type,
                'planned_aircraft_short' => $type,
                'planned_destairport' => $arrivalAirport,
                'planned_depairport' => $departureAirport,
                'aircraft_id' => Aircraft::where('code', $type)->first()->id,
            ]
        );
    }
}
