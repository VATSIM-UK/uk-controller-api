<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;

class OriginAirfieldStandAllocatorTest extends BaseFunctionalTestCase
{
    private readonly OriginAirfieldStandAllocator $allocator;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(OriginAirfieldStandAllocator::class);
    }

    public function testItAllocatesAStandWithAnAppropriateAerodromeReferenceCode()
    {
        Aircraft::where('code', 'B738')->update(['aerodrome_reference_code' => 'E']);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'aerodrome_reference_code' => 'E',
            ]
        );

        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'aerodrome_reference_code' => 'D',
            ]
        );

        $aircraft = $this->createAircraft('BAW23451', 'EGKR', 'EGGD');
        $this->assertEquals($weightAppropriateStand->id, $this->allocator->allocate($aircraft));
    }

    public function testItAllocatesAStandInAerodromeReferenceAscendingOrder()
    {
        Aircraft::where('code', 'B738')->update(['aerodrome_reference_code' => 'B']);
        $weightAppropriateStand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '502',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'aerodrome_reference_code' => 'B',
            ]
        );

        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'aerodrome_reference_code' => 'D',
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
                'aerodrome_reference_code' => 'E',
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
                'aeordrome_reference_code' => 'E',
            ]
        );

        $stand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EG',
                'aerodrome_reference_code' => 'E',
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
                'aerodrome_reference_code' => 'E',
            ]
        );

        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EG',
                'aerodrome_reference_code' => 'E',
            ]
        );

        $stand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '17',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGG',
                'aerodrome_reference_code' => 'E',
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
                'aerodrome_reference_code' => 'E',
            ]
        );

        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EG',
                'aerodrome_reference_code' => 'E',
            ]
        );

        Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '17',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGG',
                'aerodrome_reference_code' => 'E',
            ]
        );

        $stand = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '18',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'aerodrome_reference_code' => 'E',
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
                'aerodrome_reference_code' => 'E',
            ]
        );

        $stand2 = Stand::create(
            [
                'airfield_id' => 3,
                'identifier' => '16',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'origin_slug' => 'EGGD',
                'aerodrome_reference_code' => 'E',
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
                'aerodrome_reference_code' => 'E',
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
                'aircraft_id' => 1,
            ]
        );
    }
}
