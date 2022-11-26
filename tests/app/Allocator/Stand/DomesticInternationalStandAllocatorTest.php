<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandType;
use App\Models\Vatsim\NetworkAircraft;
use util\Traits\WithWakeCategories;

class DomesticInternationalStandAllocatorTest extends BaseFunctionalTestCase
{
    use WithWakeCategories;

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
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
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
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
                'type_id' => StandType::international()->first()->id,
                'assignment_priority' => 1,
            ]
        );
    }

    public function testItAssignsADomesticStand()
    {
        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL', true);
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($this->domesticStand->id, $assignment);
    }

    public function testItAssignsAnInternationalStand()
    {
        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL', false);
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($this->internationalStand->id, $assignment);
    }

    public function testItAssignsWeightAppropriateStands()
    {
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55C',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'type_id' => StandType::domestic()->first()->id,
                'assignment_priority' => 55,
            ]
        );
        $this->setWakeCategoryForAircraft('B738', 'J');
        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL', true);
        $assignment = $this->allocator->allocate($aircraft);

        $this->assertEquals($weightAppropriateStand->id, $assignment);
    }

    public function testItAssignsInWeightAscendingOrder()
    {
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '55C',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'S')->first()->id,
                'type_id' => StandType::domestic()->first()->id,
                'assignment_priority' => 55,
            ]
        );
        $this->setWakeCategoryForAircraft('B738', 'S');
        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL', true);
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
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'assignment_priority' => 2,
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL', true);
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
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
                'type_id' => StandType::domestic()->first()->id,
                'assignment_priority' => 1,
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'B738', 'EGLL', true);
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
        $aircraft = NetworkAircraft::create(
            [
                'callsign' => 'BAW898',
                'planned_aircraft' => 'B738',
                'planned_aircraft_short' => 'B738',
                'planned_destairport' => '',
                'planned_depairport' => 'EIDW',
            ]
        );
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItReturnsNothingOnNoStandAllocated()
    {
        $this->assertNull($this->allocator->allocate($this->createAircraft('BAW898', 'B738', 'XXXX', true)));
    }

    private function createAircraft(
        string $callsign,
        string $type,
        string $arrivalAirport,
        bool $domestic
    ): NetworkAircraft {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'planned_aircraft' => $type,
                'planned_aircraft_short' => $type,
                'planned_destairport' => $arrivalAirport,
                'planned_depairport' => $domestic ? 'EGKK' : 'EIDW',
            ]
        );
    }
}
