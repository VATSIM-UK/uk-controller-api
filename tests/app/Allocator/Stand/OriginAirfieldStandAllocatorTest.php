<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

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

    public function testItGetsRankedStandAllocation()
    {
        // Create an airfield that we dont have so we know its a clean test
        $airfield = Airfield::factory()->create(['code' => 'EXXX']);
        $airfieldId = $airfield->id;

        // Create a small aircraft type to test stand size ranking
        $cessna = Aircraft::create(
            [
                'code' => 'C172',
                'allocate_stands' => true,
                'aerodrome_reference_code' => 'A',
                'wingspan' => 1,
                'length' => 12,
            ]
        );

        // Should be ranked first - it has the highest priority. It gets a stand reservation to make
        // sure it is ranked first even if it is occupied.
        $standA1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'A1',
                'origin_slug' => 'EGGD',
                'assignment_priority' => 100,
                'aerodrome_reference_code' => 'C'
            ]
        );
        StandReservation::create(
            [
                'stand_id' => $standA1->id,
                'start' => Carbon::now()->subMinutes(1),
                'end' => Carbon::now()->addMinutes(1),
            ]
        );

        // Should be ranked joint second, bigger than A1 but same priority
        $standB1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'B1',
                'origin_slug' => 'EGGD',
                'assignment_priority' => 100,
            ]
        );
        StandRequest::factory()->create(['requested_time' => Carbon::now(), 'stand_id' => $standB1->id]);
        $standB2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'B2',
                'origin_slug' => 'EGGD',
                'assignment_priority' => 100,
            ]
        );

        // Should be ranked joint third, same size as B1 but lower priority
        $standC1 = Stand::factory()->create(
            ['airfield_id' => $airfieldId, 'identifier' => 'C1', 'origin_slug' => 'EGGD', 'assignment_priority' => 101]
        );
        $standC2 = Stand::factory()->create(
            ['airfield_id' => $airfieldId, 'identifier' => 'C2', 'origin_slug' => 'EGGD', 'assignment_priority' => 101]
        );

        // Should be ranked 4th, 5th, 6th, less specific destinations slugs
        $standC3 = Stand::factory()->create(
            ['airfield_id' => $airfieldId, 'identifier' => 'C3', 'origin_slug' => 'EGG', 'assignment_priority' => 101]
        );
        $standC4 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C4',
                'origin_slug' => 'EG',
                'assignment_priority' => 101
            ]
        );
        $standC5 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C5',
                'origin_slug' => 'E',
                'assignment_priority' => 101
            ]
        );

        // Should not appear in rankings - wrong airfield
        Stand::factory()->create(['airfield_id' => 2, 'identifier' => 'D1']);

        // Should not appear in rankings - wrong origin slug
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'E1',
                'origin_slug' => 'EGKK',
            ]
        );

        // Should not appear in rankings - is cargo
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'E2',
                'origin_slug' => 'EGGD',
                'type_id' => 3,
            ]
        );

        // Should not appear in rankings - no origin slug
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'E3',
            ]
        );

        // Should not appear in rankings - too small ARC
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'F1',
                'aerodrome_reference_code' => 'A',
                'origin_slug' => 'EGGD',
            ]
        );

        // Should not appear in rankings - too small max aircraft size
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'G1',
                'max_aircraft_id_length' => $cessna->id,
                'max_aircraft_id_wingspan' => $cessna->id,
                'origin_slug' => 'EGGD',
            ]
        );

        // Should not appear in rankings - closed
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'H1',
                'aerodrome_reference_code' => 'C',
                'closed_at' => Carbon::now(),
                'origin_slug' => 'EGGD',
            ]
        );

        $expectedRanks = [
            $standA1->id => 1,
            $standB1->id => 2,
            $standB2->id => 2,
            $standC1->id => 3,
            $standC2->id => 3,
            $standC3->id => 4,
            $standC4->id => 5,
            $standC5->id => 6
        ];

        $actualRanks = $this->allocator->getRankedStandAllocation(
            $this->newAircraft('BAW23451', $airfield->code, 'EGGD')
        )->mapWithKeys(
                fn($stand) => [$stand->id => $stand->rank]
            )
            ->toArray();

        $this->assertEquals($expectedRanks, $actualRanks);
    }

    private function createAircraft(
        string $callsign,
        string $arrivalAirport,
        string $departureAirport
    ): NetworkAircraft {
        return tap(
            $this->newAircraft($callsign, $arrivalAirport, $departureAirport),
            fn(NetworkAircraft $aircraft) => $aircraft->save()
        );
    }

    private function newAircraft(
        string $callsign,
        string $arrivalAirport,
        string $departureAirport
    ): NetworkAircraft {
        return new NetworkAircraft(
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
