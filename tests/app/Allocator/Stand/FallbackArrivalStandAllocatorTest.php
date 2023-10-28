<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandReservation;
use App\Models\Stand\StandType;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

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

    public function testItDoesntRankStandsIfUnknownAircraft()
    {
        $aircraft = $this->newAircraft('BAW123', 'XXX', 'EGLL', 'EIDW');
        $this->assertEquals(collect(), $this->allocator->getRankedStandAllocation($aircraft));
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

        // Should be ranked first - its the smallest stand that's applicable
        $standA1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'A1',
                'assignment_priority' => 100,
                'aerodrome_reference_code' => 'E',
            ]
        );
        StandReservation::create(
            [
                'stand_id' => $standA1->id,
                'start' => Carbon::now()->subMinutes(1),
                'end' => Carbon::now()->addMinutes(1),
            ]
        );

        // Should be ranked joint second, bigger than A1, but same priority
        $standB1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'B1',
                'assignment_priority' => 100,
            ]
        );
        StandRequest::factory()->create(['requested_time' => Carbon::now(), 'stand_id' => $standB1->id]);
        $standB2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'B2',
                'assignment_priority' => 100,
            ]
        );

        // Should be ranked joint third, same size as B1 and B2, but lower priority
        $standC1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C1',
                'assignment_priority' => 101,
            ]
        );
        $standC2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C2',
                'assignment_priority' => 101,
            ]
        );

        // Should not appear in rankings - wrong airfield
        Stand::factory()->create(['airfield_id' => 2, 'identifier' => 'D1', 'type_id' => 1]);

        // Should not appear in rankings - is cargo
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'E1',
                'type_id' => 3,
            ]
        );

        // Should not appear in rankings - too small ARC
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'F1',
                'aerodrome_reference_code' => 'A'
            ]
        );

        // Should not appear in rankings - too small max aircraft size
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'G1',
                'max_aircraft_length' => $cessna->length,
                'max_aircraft_wingspan' => $cessna->wingspan
            ]
        );

        // Should not appear in rankings - closed
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'H1',
                'aerodrome_reference_code' => 'E',
                'closed_at' => Carbon::now()
            ]
        );

        $expectedRanks = [
            $standA1->id => 1,
            $standB1->id => 2,
            $standB2->id => 2,
            $standC1->id => 3,
            $standC2->id => 3,
        ];

        $actualRanks = $this->allocator->getRankedStandAllocation(
            $this->newAircraft('VIR22F', 'B738', $airfield->code)
        )->mapWithKeys(
                fn($stand) => [$stand->id => $stand->rank]
            )
            ->toArray();

        $this->assertEquals($expectedRanks, $actualRanks);
    }

    private function createAircraft(
        string $callsign,
        string $type,
        string $arrivalAirport
    ): NetworkAircraft {
        return tap(
            $this->newAircraft($callsign, $type, $arrivalAirport),
            fn(NetworkAircraft $aircraft) =>
            $aircraft->save()
        );
    }

    private function newAircraft(
        string $callsign,
        string $type,
        string $arrivalAirport
    ): NetworkAircraft {
        return new NetworkAircraft(
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
