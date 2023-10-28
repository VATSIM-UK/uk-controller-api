<?php

namespace App\Models\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Airline\Airline;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StandTest extends BaseFunctionalTestCase
{
    public function testUnoccupiedOnlyReturnsFreeStands()
    {
        // Create a stand pairing between stand 2 and stand 3, and occupy it.
        Stand::find(2)->pairedStands()->sync([3]);
        Stand::find(3)->pairedStands()->sync([2]);

        NetworkAircraft::find('BAW123')->occupiedStand()->sync([2]);
        $stands = Stand::unoccupied()->get()->pluck('id')->toArray();
        $this->assertEquals([1], $stands);
    }

    public function testUnassignedOnlyReturnsUnassignedStands()
    {
        // Create a stand pairing between stand 2 and stand 3, and assign it.
        Stand::find(2)->pairedStands()->sync([3]);
        Stand::find(3)->pairedStands()->sync([2]);

        StandAssignment::create(['callsign' => 'BAW123', 'stand_id' => 2]);

        $stands = Stand::unassigned()->get()->pluck('id')->toArray();
        $this->assertEquals([1], $stands);
    }

    public function testAvailableOnlyReturnsUnassignedUnoccupiedUnreservedOpenStands()
    {
        $extraStand = Stand::find(1)->replicate();
        $extraStand->identifier = 'NEW';
        $extraStand->save();

        StandReservation::create(
            [
                'stand_id' => $extraStand->id,
                'start' => Carbon::now()->subMinutes(1),
                'end' => Carbon::now()->addHour(),
            ]
        );

        NetworkAircraft::find('BAW123')->occupiedStand()->sync([1]);
        StandAssignment::create(['callsign' => 'BAW123', 'stand_id' => 2]);

        $closedStand = Stand::find(1)->replicate();
        $closedStand->identifier = 'CLOSED';
        $closedStand->closed_at = Carbon::now();
        $closedStand->save();

        $stands = Stand::available()->get()->pluck('id')->toArray();
        $this->assertEquals([3], $stands);
    }

    public function testAirlineOnlyReturnsStandsForTheCorrectAirline()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => 'EGGD',
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'destination' => null,
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'destination' => 'EGGD',
                ],
            ]
        );

        $stands = Stand::airline(
            Airline::find(1)
        )->pluck('stands.id')->toArray();
        $this->assertEquals([1, 2], $stands);
    }

    public function testAirlineOnlyReturnsStandsAtTheRightTime()
    {
        Carbon::setTestNow(Carbon::parse('2020-12-05 16:00:00'));
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => 'EGGD',
                    'not_before' => '16:00:01',
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'destination' => 'EGGD',
                    'not_before' => null,
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'destination' => 'EGGD',
                    'not_before' => '16:00:00',
                ],
            ]
        );

        $stands = Stand::airline(
            Airline::find(1)
        )->pluck('stands.id')->toArray();
        $this->assertEquals([2, 3], $stands);
    }

    public function testAirlineCallsignOnlyReturnsStandsForTheCorrectAirlineAndCallsigns()
    {
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'callsign_slug' => '123',
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => '456',
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'callsign_slug' => '123',
                ],
            ]
        );

        $stands = Stand::airlineCallsign(
            Airline::find(1),
            ['123']
        )->pluck('stands.id')->toArray();
        $this->assertEquals([1], $stands);
    }

    public function testAirlineCallsignOnlyReturnsStandsWithinTheRightTime()
    {
        Carbon::setTestNow(Carbon::parse('2020-12-05 16:00:00'));
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'callsign_slug' => '123',
                    'not_before' => '16:00:01',
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'callsign_slug' => '123',
                    'not_before' => null,
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 3,
                    'callsign_slug' => '123',
                    'not_before' => '16:00:00',
                ],
            ]
        );

        $stands = Stand::airlineCallsign(
            Airline::find(1),
            ['123']
        )->pluck('stands.id')->toArray();
        $this->assertEquals([2, 3], $stands);
    }

    public function testAppropriateDimensionsOnlyReturnsStandsThatAreTheRightSize()
    {
        $a330 = Aircraft::where('code', 'A333')->first();
        $b738 = Aircraft::where('code', 'B738')->first();
        Stand::find(1)->update(
            [
                'max_aircraft_length' => $a330->length,
                'max_aircraft_wingspan' => $b738->wingspan,
                'aerodrome_reference_code' => 'E',
            ]
        );
        Stand::find(2)->update(
            [
                'max_aircraft_length' => $b738->length,
                'max_aircraft_wingspan' => $a330->wingspan,
                'aerodrome_reference_code' => 'E',
            ]
        );
        Stand::find(3)->update(
            [
                'max_aircraft_length' => $a330->length,
                'max_aircraft_wingspan' => $a330->wingspan,
                'aerodrome_reference_code' => 'E',
            ]
        );

        $stands = Stand::appropriateDimensions($a330)->get()->pluck('id')->toArray();

        $this->assertEquals([3], $stands);
    }

    public function testAppropriateDimensionsReturnsStandsWithNoMaxWingspan()
    {
        $a330 = Aircraft::where('code', 'A333')->first();
        Stand::find(1)->update(['max_aircraft_id_length' => $a330->id, 'aerodrome_reference_code' => 'E']);
        Stand::find(2)->update(['max_aircraft_id_length' => $a330->id, 'aerodrome_reference_code' => 'E']);
        Stand::find(3)->update(
            [
                'max_aircraft_id_length' => $a330->id,
                'max_aircraft_id_wingspan' => $a330->id,
                'aerodrome_reference_code' => 'E',
            ]
        );
        $stands = Stand::appropriateDimensions($a330)->get()->pluck('id')->toArray();
        $this->assertEquals([1, 2, 3], $stands);
    }

    public function testAppropriateDimensionsReturnsStandsWithNoMaxLength()
    {
        $a330 = Aircraft::where('code', 'A333')->first();
        Stand::find(1)->update(
            [
                'max_aircraft_id_length' => $a330->id,
                'max_aircraft_id_wingspan' => $a330->id,
                'aerodrome_reference_code' => 'E',
            ]
        );
        Stand::find(2)->update(
            [
                'max_aircraft_id_length' => $a330->id,
                'max_aircraft_id_wingspan' => $a330->id,
                'aerodrome_reference_code' => 'E',
            ]
        );
        Stand::find(3)->update(['max_aircraft_id_wingspan' => $a330->id, 'aerodrome_reference_code' => 'E']);
        $stands = Stand::appropriateDimensions($a330)->get()->pluck('id')->toArray();
        $this->assertEquals([1, 2, 3], $stands);
    }

    public function testAppropriateDimensionsRejectsStandsThatArentDeepEnough()
    {
        $a330 = Aircraft::where('code', 'A333')->first();
        $b738 = Aircraft::where('code', 'B738')->first();
        $b738->update(['wingspan' => 999.99]);

        Stand::find(1)->update(
            [
                'max_aircraft_length' => $a330->length,
                'max_aircraft_wingspan' => $a330->wingspan,
                'aerodrome_reference_code' => 'E',
            ]
        );
        Stand::find(2)->update(
            [
                'max_aircraft_length' => $b738->length,
                'max_aircraft_wingspan' => $b738->wingspan,
                'aerodrome_reference_code' => 'E',
            ]
        );
        Stand::find(3)->update(
            [
                'max_aircraft_length' => $a330->length,
                'max_aircraft_wingspan' => $a330->wingspan,
                'aerodrome_reference_code' => 'E',
            ]
        );

        $stands = Stand::appropriateDimensions($a330)->get()->pluck('id')->toArray();

        $this->assertEquals([1, 3], $stands);
    }

    public function testAppropriateDimensionsRejectsStandsThatArentWideEnough()
    {
        $a330 = Aircraft::where('code', 'A333')->first();
        $b738 = Aircraft::where('code', 'B738')->first();
        $b738->update(['length' => 999.99]);

        Stand::find(1)->update(
            [
                'max_aircraft_length' => $a330->length,
                'max_aircraft_wingspan' => $a330->wingspan,
                'aerodrome_reference_code' => 'E',
            ]
        );
        Stand::find(2)->update(
            [
                'max_aircraft_length' => $b738->length,
                'max_aircraft_wingspan' => $b738->wingspan,
                'aerodrome_reference_code' => 'E',
            ]
        );
        Stand::find(3)->update(
            [
                'max_aircraft_length' => $a330->length,
                'max_aircraft_wingspan' => $a330->wingspan,
                'aerodrome_reference_code' => 'E',
            ]
        );

        $stands = Stand::appropriateDimensions($a330)->get()->pluck('id')->toArray();

        $this->assertEquals([1, 3], $stands);
    }

    public function testAppropriateAerodromeReferenceCodeOnlyReturnsLargeEnoughStands()
    {
        $a330 = Aircraft::where('code', 'A333')->first();
        Stand::find(1)->update(['aerodrome_reference_code' => 'F']);
        Stand::find(2)->update(['aerodrome_reference_code' => 'D']);
        Stand::find(3)->update(['aerodrome_reference_code' => 'E']);

        $stands = Stand::appropriateAerodromeReferenceCode($a330)->get()->pluck('id')->toArray();
        sort($stands);

        $this->assertEquals([1, 3], $stands);
    }

    public function testSizeAppropriateOnlyReturnsStandsThatAreBigEnough()
    {
        $a330 = Aircraft::where('code', 'A333')->first();

        // Make this stand too small
        Stand::find(2)->update(['aerodrome_reference_code' => 'D']);

        // Make this stand too small by max aircraft size
        $b738 = Aircraft::where('code', 'B738')->first();
        $b738->update(['length' => 998.99]);
        Stand::find(1)->update(
            [
                'max_aircraft_length' => $b738->length,
                'max_aircraft_wingspan' => $b738->wingspan,
                'aerodrome_reference_code' => 'F',
            ]
        );

        // Make this stand allowable, with no max aircraft size
        Stand::find(3)->update(['aerodrome_reference_code' => 'E']);

        // Make this stand allowable, with max aircraft size
        $extraStand = Stand::factory()->create(
            [
                'aerodrome_reference_code' => 'E',
                'max_aircraft_length' => '999',
                'max_aircraft_wingspan' => '999',
            ]
        );

        $stands = Stand::sizeAppropriate($a330)->get()->pluck('id')->sort()->toArray();

        $this->assertEquals([3, $extraStand->id], $stands);
    }

    public function testNotReservedDoesntReturnStandsThatAreCurrentlyReserved()
    {
        // Stand 1 is allowed because the reservation has expired
        StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now()->subMinutes(10),
                'end' => Carbon::now()->subMinutes(5),
            ]
        );

        // Stand 2 is not allowed because its reserved
        StandReservation::create(
            [
                'stand_id' => 2,
                'start' => Carbon::now()->subMinutes(10),
                'end' => Carbon::now()->addMinutes(5),
            ]
        );

        // Stand 3 is allowed because the reservation hasn't started
        StandReservation::create(
            [
                'stand_id' => 3,
                'start' => Carbon::now()->addMinute(),
                'end' => Carbon::now()->addHour(),
            ]
        );

        // Extra stand is allowed because it has no reservations
        $extraStand = Stand::find(1)->replicate();
        $extraStand->identifier = 'NEW';
        $extraStand->save();

        $stands = Stand::notReserved()->get()->pluck('id')->toArray();
        $this->assertEquals([1, 3, $extraStand->id], $stands);
    }

    public function testOrderByAssignmentPriorityOrdersAscending()
    {
        Stand::where('id', 1)->update(['assignment_priority' => 2]);
        Stand::where('id', 2)->update(['assignment_priority' => 3]);
        Stand::where('id', 3)->update(['assignment_priority' => 1]);

        $stands = Stand::orderByAssignmentPriority()->get()->pluck('id')->toArray();
        $this->assertEquals([3, 1, 2], $stands);
    }

    public function testOrderByAssignmentPriorityOrdersDescending()
    {
        Stand::where('id', 1)->update(['assignment_priority' => 2]);
        Stand::where('id', 2)->update(['assignment_priority' => 3]);
        Stand::where('id', 3)->update(['assignment_priority' => 1]);

        $stands = Stand::orderByAssignmentPriority('desc')->get()->pluck('id')->toArray();
        $this->assertEquals([2, 1, 3], $stands);
    }

    public function testOrderByAerodromeReferenceCodeAscending()
    {
        Stand::find(1)->update(['aerodrome_reference_code' => 'F']);
        Stand::find(2)->update(['aerodrome_reference_code' => 'D']);
        Stand::find(3)->update(['aerodrome_reference_code' => 'E']);

        $stands = Stand::orderByAerodromeReferenceCode()->get()->pluck('id')->toArray();
        $this->assertEquals([2, 3, 1], $stands);
    }

    public function testOrderByAerodromeReferenceCodeDescending()
    {
        Stand::find(1)->update(['aerodrome_reference_code' => 'F']);
        Stand::find(2)->update(['aerodrome_reference_code' => 'D']);
        Stand::find(3)->update(['aerodrome_reference_code' => 'E']);

        $stands = Stand::orderByAerodromeReferenceCode('desc')->get()->pluck('id')->toArray();
        $this->assertEquals([1, 3, 2], $stands);
    }
}
