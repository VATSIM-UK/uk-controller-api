<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;

class SizeAppropriateStandAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var SizeAppropriateStandAllocator
     */
    private $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(SizeAppropriateStandAllocator::class);
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
            ]
        );
        $stand->refresh();

        Aircraft::create(
            [
                'code' => 'A388',
                'wake_category_id' => WakeCategory::where('code', 'J')->first()->id,
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'A388', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);
        $expectedAssignment = StandAssignment::where('callsign', 'AEU252')->first();

        $this->assertEquals($expectedAssignment->stand_id, $assignment->stand_id);
        $this->assertEquals($expectedAssignment->callsign, $assignment->callsign);
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
            ]
        );
        $stand->refresh();

        Aircraft::create(
            [
                'code' => 'B744',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );

        $aircraft = $this->createAircraft('AEU252', 'B744', 'EGLL');
        $assignment = $this->allocator->allocate($aircraft);
        $expectedAssignment = StandAssignment::where('callsign', 'AEU252')->first();

        $this->assertEquals($expectedAssignment->stand_id, $assignment->stand_id);
        $this->assertEquals($expectedAssignment->callsign, $assignment->callsign);
        $this->assertEquals('AEU252', $assignment->callsign);
    }

    public function testItDoesntAssignIfNoAircraftType()
    {
        $aircraft = $this->createAircraft('AEU252', 'ABCD', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
        $this->assertNull(StandAssignment::where('callsign', 'AEU252')->first());
    }

    private function createAircraft(
        string $callsign,
        string $type,
        string $arrivalAirport,
        string $rules = 'I'
    ): NetworkAircraft {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'planned_aircraft' => $type,
                'planned_destairport' => $arrivalAirport,
                'planned_flighttype' => $rules,
            ]
        );
    }
}
