<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\WakeCategory;
use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\DB;
use util\Traits\WithWakeCategories;

class OriginAirfieldStandAllocatorTest extends BaseFunctionalTestCase
{
    use WithWakeCategories;

    private readonly OriginAirfieldStandAllocator $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(OriginAirfieldStandAllocator::class);
    }

    public function testItAllocatesAStandWithAnAppropriateWeight()
    {
        $this->setWakeCategoryForAircraft('B738', 'UM');
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '15',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'wake_category_id' => WakeCategory::where('code', 'UM')->first()->id,
            ]
        );

        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'wake_category_id' => WakeCategory::where('code', 'L')->first()->id,
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGKR', 'EGGD');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesAStandInWeightAscendingOrder()
    {
        $this->setWakeCategoryForAircraft('B738', 'S');
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '15',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'wake_category_id' => WakeCategory::where('code', 'S')->first()->id,
            ]
        );

        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGKR', 'EGGD');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesSingleCharacterMatches()
    {
        $stand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '15',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'E',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGKR', 'EGGD');
        $this->assertEquals($stand->id, $this->allocator->allocate($aircraft));
    }

    public function testItPrefersDoubleCharacterMatches()
    {
        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '15',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'E',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );

        $stand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EG',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGKR', 'EGGD');
        $this->assertEquals($stand->id, $this->allocator->allocate($aircraft));
    }

    public function testItPrefersTripleCharacterMatches()
    {
        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '15',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'E',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );

        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EG',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );

        $stand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '17',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGG',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGKR', 'EGGD');
        $this->assertEquals($stand->id, $this->allocator->allocate($aircraft));
    }

    public function testItPrefersFullMatches()
    {
        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '15',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'E',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );

        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EG',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );

        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '17',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGG',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );

        $stand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '18',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );
        $aircraft = $this->createAircraft('BAW23451', 'EGKR', 'EGGD');
        $this->assertEquals($stand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateOccupiedStands()
    {
        $stand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '15',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );

        $stand2 = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'wake_category_id' => WakeCategory::where('code', 'UM')->first()->id,
            ]
        );

        $occupier = $this->createAircraft('EZY7823', 'EGKR', 'EGGD');
        $occupier->occupiedStand()->sync([$stand2->id]);
        $aircraft = $this->createAircraft('BAW23451', 'EGKR', 'EGGD');
        $this->assertEquals($stand->id, $this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateAStandWithNoDestination()
    {
        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '15',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => null,
                'wake_category_id' => WakeCategory::where('code', 'H')->first()->id,
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGKR', 'EGGD');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    private function createAircraft(
        string $callsign,
        string $arrivalAirport,
        string $departureAirport
    ): NetworkAircraft
    {
        return NetworkAircraft::create(
            [
                'callsign' => $callsign,
                'cid' => 1234,
                'planned_aircraft' => 'B738',
                'planned_aircraft_short' => 'B738',
                'planned_destairport' => $arrivalAirport,
                'planned_depairport' => $departureAirport,
            ]
        );
    }
}
