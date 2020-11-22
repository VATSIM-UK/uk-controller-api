<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;

class AirlineTerminalArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var AirlineArrivalStandAllocator
     */
    private $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(AirlineTerminalArrivalStandAllocator::class);
        Airline::where('icao_code', 'BAW')->first()->terminals()->attach(2);
        Stand::find(1)->update(['terminal_id' => 1]);
        Stand::find(2)->update(['terminal_id' => 2]);
    }

    public function testItAllocatesAStandAtTheRightTerminal()
    {
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals(2, $this->allocator->allocate($aircraft)->stand_id);
        $this->assertEquals(2, StandAssignment::find($aircraft->callsign)->stand_id);
    }

    public function testItAllocatesStandsInWeightAscendingOrder()
    {
        Aircraft::where('code', 'B738')->update(['wake_category_id' => WakeCategory::where('code', 'S')->first()->id]);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'S')->first()->id,
                'terminal_id' => 2,
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft)->stand_id);
        $this->assertEquals($weightAppropriateStand->id, StandAssignment::find($aircraft->callsign)->stand_id);
    }

    public function testItAllocatesStandsAtAppropriateWeight()
    {
        Aircraft::where('code', 'B738')->update(['wake_category_id' => WakeCategory::where('code', 'UM')->first()->id]);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'UM')->first()->id,
                'terminal_id' => 2,
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft)->stand_id);
        $this->assertEquals($weightAppropriateStand->id, StandAssignment::find($aircraft->callsign)->stand_id);
    }

    public function testItDoesntAllocateOccupiedStands()
    {
        $extraStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'UM')->first()->id,
                'terminal_id' => 2,
            ]
        );

        $occupier = $this->createAircraft('EZY7823', 'EGLL');
        $occupier->occupiedStand()->sync([2]);
        $aircraft = $this->createAircraft('BAW23451', 'EGLL');

        $this->assertEquals($extraStand->id, $this->allocator->allocate($aircraft)->stand_id);
        $this->assertEquals($extraStand->id, StandAssignment::find($aircraft->callsign)->stand_id);
    }

    public function testItDoesntAllocateUnavailableStands()
    {
        $extraStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'wake_category_id' => WakeCategory::where('code', 'UM')->first()->id,
                'terminal_id' => 2,
            ]
        );
        NetworkAircraft::find('BAW123')->occupiedStand()->sync([2]);

        $aircraft = $this->createAircraft('BAW23451', 'EGLL');
        $this->assertEquals($extraStand->id, $this->allocator->allocate($aircraft)->stand_id);
        $this->assertEquals($extraStand->id, StandAssignment::find($aircraft->callsign)->stand_id);
    }

    public function testItDoesntAllocateNonExistentAirlines()
    {
        $aircraft = $this->createAircraft('***1234', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
        $this->assertNull(StandAssignment::find($aircraft->callsign));
    }

    public function testItReturnsNullOnNoStandAllocated()
    {
        Stand::all()->each(function (Stand $stand) {
            $stand->delete();
        });
        $aircraft = $this->createAircraft('BAW999', 'EGLL');
        $this->assertNull($this->allocator->allocate($aircraft));
        $this->assertNull(StandAssignment::find($aircraft->callsign));
    }

    private function createAircraft(string $callsign, string $arrivalAirport): NetworkAircraft
    {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'planned_aircraft' => 'B738',
                'planned_destairport' => $arrivalAirport]
        );
    }
}
