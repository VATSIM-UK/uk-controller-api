<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandReservation;
use App\Models\Stand\StandType;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessAviationFlightArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    private BusinessAviationFlightPreferredArrivalStandAllocator $allocator;

    private Stand $baStand;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(BusinessAviationFlightPreferredArrivalStandAllocator::class);

        // Create a BA aircraft type and a normal one
        Aircraft::firstOrCreate(
            ['code' => 'C25C'],
            [
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 1.0,
                'aerodrome_reference_code' => 'A',
            ]
        );

        Aircraft::firstOrCreate(
            ['code' => 'B744'],
            [
                'allocate_stands' => true,
                'wingspan' => 1.0,
                'length' => 2.2,
                'aerodrome_reference_code' => 'E',
            ]
        );

        // Make a normal BA stand
        $this->baStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'BA-01',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
                'type_id' => StandType::where('key', 'BUSINESS AVIATION')->first()->id,
            ]
        );

        // Create another stand that's not BA
        Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'DOM-01',
                'latitude' => 54.65875500,
                'longitude' => -6.22258694,
                'aerodrome_reference_code' => 'E',
                'type_id' => StandType::where('key', 'DOMESTIC')->first()->id,
            ]
        );

        $airlineId = Airline::where('icao_code', 'VIR')->first()->id;
        $this->attachStandToAirline($this->baStand->id, $airlineId);
    }

    public function testItAllocatesBusinessAviationStandsIfAircraftIsBusinessAviation()
    {
        $aircraft = $this->createAircraft('VIRBA1', 'EGLL', 'C25C');
        $allocation = $this->allocator->allocate($aircraft);
        $this->assertEquals($this->baStand->id, $allocation);
    }

    public function testItReturnsNothingIfNoStandsToAllocated()
    {
        $this->baStand->delete();
        $aircraft = $this->createAircraft('VIRBA1', 'EGLL', 'C25C');
        $this->assertNull($this->allocator->allocate($aircraft));
    }

    public function testItDoesntAllocateOccupiedStands()
    {
        StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => $this->baStand->id
            ]
        );
        $aircraft = $this->createAircraft('VIRBA1', 'EGLL', 'C25C');
        $allocation = $this->allocator->allocate($aircraft);
        $this->assertNull($allocation);
    }

    public function testItDoesntAllocateBusinessAviationStandsIfAircraftNotBusinessAviation()
    {
        $aircraft = $this->createAircraft('VIR22F', 'EGLL', 'B744');
        $allocation = $this->allocator->allocate($aircraft);
        $this->assertNull($allocation);
    }

    public function testItDoesntRankStandsIfUnknownAircraft()
    {
        $aircraft = $this->newAircraft('BAW1234', 'EGLL', 'C172');

        $this->assertEquals(collect(), $this->allocator->getRankedStandAllocation($aircraft));
    }

    private function attachStandToAirline(int $standId, int $airlineId): void
    {
        DB::table('airline_stand')->insert(
            [
                'stand_id' => $standId,
                'airline_id' => $airlineId,
                'priority' => 1,
                'not_before' => null,
                'destination' => null,
                'callsign_slug' => null,
                'full_callsign' => null,
                'aircraft_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
    }

    private function createAircraft(
        string $callsign,
        string $arrivalAirport,
        string $aircraftType = 'C25C'
    ): NetworkAircraft {
        return tap(
            $this->newAircraft($callsign, $arrivalAirport, $aircraftType),
            fn (NetworkAircraft $aircraft) => $aircraft->save()
        );
    }

    private function newAircraft(
        string $callsign,
        string $arrivalAirport,
        string $aircraftType = 'C25C'
    ): NetworkAircraft {
        return new NetworkAircraft(
            [
                'callsign' => $callsign,
                'cid' => 1234,
                'planned_aircraft' => $aircraftType,
                'planned_aircraft_short' => $aircraftType,
                'planned_destairport' => $arrivalAirport,
                'airline_id' => Airline::where('icao_code', 'VIR')->first()->id,
                'aircraft_id' => Aircraft::where('code', $aircraftType)->first()?->id,
            ]
        );
    }
}
