<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandReservation;
use App\Models\Stand\StandType;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Carbon;

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

    public function testItDoesntRankStandsIfUnknownAircraft()
    {
        $aircraft = $this->newAircraft('BAW123', 'XXX', 'EGLL', 'EIDW');
        $this->assertEquals(collect(), $this->allocator->getRankedStandAllocation($aircraft));
    }

    public function testItGetsRankedStandAllocationForDomestic()
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
                'type_id' => 1,
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
                'type_id' => 1,
            ]
        );
        StandRequest::factory()->create(['requested_time' => Carbon::now(), 'stand_id' => $standB1->id]);
        $standB2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'B2',
                'assignment_priority' => 100,
                'type_id' => 1,
            ]
        );

        // Should be ranked joint third, same size as B1 and B2, but lower priority
        $standC1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C1',
                'assignment_priority' => 101,
                'type_id' => 1,
            ]
        );
        $standC2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C2',
                'assignment_priority' => 101,
                'type_id' => 1,
            ]
        );

        // Should not appear in rankings - wrong airfield
        Stand::factory()->create(['airfield_id' => 2, 'identifier' => 'D1', 'type_id' => 1]);

        // Should not appear in rankings - not domestic
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'E1',
                'type_id' => 2,
            ]
        );

        // Should not appear in rankings - too small ARC
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'F1',
                'aerodrome_reference_code' => 'A',
                'type_id' => 1,
            ]
        );

        // Should not appear in rankings - too small max aircraft size
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'G1',
                'max_aircraft_id_length' => $cessna->id,
                'max_aircraft_id_wingspan' => $cessna->id,
                'type_id' => 1,
            ]
        );

        // Should not appear in rankings - closed
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'H1',
                'aerodrome_reference_code' => 'E',
                'closed_at' => Carbon::now(),
                'type_id' => 1,
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
            $this->newAircraft('VIR22F', 'B738', $airfield->code, 'EGLL')
        )->mapWithKeys(
                fn($stand) => [$stand->id => $stand->rank]
            )
            ->toArray();

        $this->assertEquals($expectedRanks, $actualRanks);
    }

    public function testItGetsRankedStandAllocationForInternational()
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
                'type_id' => 2,
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
                'type_id' => 2,
            ]
        );
        StandRequest::factory()->create(['requested_time' => Carbon::now(), 'stand_id' => $standB1->id]);
        $standB2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'B2',
                'assignment_priority' => 100,
                'type_id' => 2,
            ]
        );

        // Should be ranked joint third, same size as B1 and B2, but lower priority
        $standC1 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C1',
                'assignment_priority' => 101,
                'type_id' => 2,
            ]
        );
        $standC2 = Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'C2',
                'assignment_priority' => 101,
                'type_id' => 2,
            ]
        );

        // Should not appear in rankings - wrong airfield
        Stand::factory()->create(['airfield_id' => 2, 'identifier' => 'D1', 'type_id' => 1]);

        // Should not appear in rankings - is domestic
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'E1',
                'type_id' => 1,
            ]
        );

        // Should not appear in rankings - too small ARC
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'F1',
                'aerodrome_reference_code' => 'A',
                'type_id' => 1,
            ]
        );

        // Should not appear in rankings - too small max aircraft size
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'G1',
                'max_aircraft_id_length' => $cessna->id,
                'max_aircraft_id_wingspan' => $cessna->id,
                'type_id' => 1,
            ]
        );

        // Should not appear in rankings - closed
        Stand::factory()->create(
            [
                'airfield_id' => $airfieldId,
                'identifier' => 'H1',
                'aerodrome_reference_code' => 'E',
                'closed_at' => Carbon::now(),
                'type_id' => 1,
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
            $this->newAircraft('VIR22F', 'B738', $airfield->code, 'KJFK')
        )->mapWithKeys(
                fn($stand) => [$stand->id => $stand->rank]
            )
            ->toArray();

        $this->assertEquals($expectedRanks, $actualRanks);
    }

    private function createAircraft(
        string $callsign,
        string $type,
        string $arrivalAirport,
        string $departureAirport = 'EGKK'
    ): NetworkAircraft {
        return tap(
            $this->newAircraft($callsign, $type, $arrivalAirport, $departureAirport),
            fn(NetworkAircraft $aircraft) => $aircraft->save()
        );
    }

    private function newAircraft(
        string $callsign,
        string $type,
        string $arrivalAirport,
        string $departureAirport = 'EGKK'
    ): NetworkAircraft {
        return new NetworkAircraft(
            [
                'callsign' => $callsign,
                'cid' => 1234,
                'planned_aircraft' => $type,
                'planned_aircraft_short' => $type,
                'planned_destairport' => $arrivalAirport,
                'planned_depairport' => $departureAirport,
                'aircraft_id' => Aircraft::where('code', $type)->first()?->id,
            ]
        );
    }
}
