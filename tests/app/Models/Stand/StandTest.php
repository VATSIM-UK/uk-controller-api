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

    public function testAvailableOnlyReturnsUnassignedUnoccupiedUnreservedStands()
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

        $stands = Stand::available()->get()->pluck('id')->toArray();
        $this->assertEquals([3], $stands);
    }

    public function testAirlineOnlyReturnsStandsForTheCorrectAirline()
    {
        DB::table('airline_stand')->delete();
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => 'EGGD'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'destination' => null,
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'destination' => 'EGGD'
                ],
            ]
        );

        $stands = Stand::airline(
            Airline::find(1)
        )->get()->pluck('id')->toArray();
        $this->assertEquals([1, 2], $stands);
    }

    public function testAirlineOnlyReturnsStandsAtTheRightTime()
    {
        Carbon::setTestNow(Carbon::parse('2020-12-05 16:00:00'));
        DB::table('airline_stand')->delete();
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
        )->get()->pluck('id')->toArray();
        $this->assertEquals([2, 3], $stands);
    }

    public function testAirlineDestinationOnlyReturnsStandsForTheCorrectAirlineAndDestinations()
    {
        DB::table('airline_stand')->delete();
        DB::table('airline_stand')->insert(
            [
                [
                    'airline_id' => 1,
                    'stand_id' => 1,
                    'destination' => 'EGGD'
                ],
                [
                    'airline_id' => 1,
                    'stand_id' => 2,
                    'destination' => 'EGFF'
                ],
                [
                    'airline_id' => 2,
                    'stand_id' => 1,
                    'destination' => 'EGGD'
                ],
            ]
        );

        $stands = Stand::airlineDestination(
            Airline::find(1),
            ['EGGD']
        )->get()->pluck('id')->toArray();
        $this->assertEquals([1], $stands);
    }

    public function testAirlineDestinationOnlyReturnsStandsWithinTheRightTime()
    {
        Carbon::setTestNow(Carbon::parse('2020-12-05 16:00:00'));
        DB::table('airline_stand')->delete();
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

        $stands = Stand::airlineDestination(
            Airline::find(1),
            ['EGGD']
        )->get()->pluck('id')->toArray();
        $this->assertEquals([2, 3], $stands);
    }

    public function testAppropriateDimensionsOnlyReturnsStandsThatAreTheRightSize()
    {
        $a330 = Aircraft::where('code', 'A333')->first();
        $b738 = Aircraft::where('code', 'B738')->first();
        Stand::find(1)->update(['max_aircraft_id' => $a330->id, 'wake_category_id' => 5]);
        Stand::find(2)->update(['max_aircraft_id' => $b738->id, 'wake_category_id' => 5]);
        Stand::find(3)->update(['max_aircraft_id' => $a330->id, 'wake_category_id' => 5]);

        $stands = Stand::appropriateDimensions($a330)->get()->pluck('id')->toArray();

        $this->assertEquals([1, 3], $stands);
    }

    public function testAppropriateDimensionsReturnsStandsWithNoMaxSize()
    {
        $a330 = Aircraft::where('code', 'A333')->first();
        $stands = Stand::appropriateDimensions($a330)->get()->pluck('id')->toArray();
        $this->assertEquals([1, 2, 3], $stands);
    }

    public function testAppropriateDimensionsRejectsStandsThatArentDeepEnough()
    {
        $a330 = Aircraft::where('code', 'A333')->first();
        $b738 = Aircraft::where('code', 'B738')->first();
        $b738->update(['wingspan' => 999.99]);

        Stand::find(1)->update(['max_aircraft_id' => $a330->id, 'wake_category_id' => 5]);
        Stand::find(2)->update(['max_aircraft_id' => $b738->id, 'wake_category_id' => 5]);
        Stand::find(3)->update(['max_aircraft_id' => $a330->id, 'wake_category_id' => 5]);

        $stands = Stand::appropriateDimensions($a330)->get()->pluck('id')->toArray();

        $this->assertEquals([1, 3], $stands);
    }

    public function testAppropriateDimensionsRejectsStandsThatArentWideEnough()
    {
        $a330 = Aircraft::where('code', 'A333')->first();
        $b738 = Aircraft::where('code', 'B738')->first();
        $b738->update(['length' => 999.99]);

        Stand::find(1)->update(['max_aircraft_id' => $a330->id, 'wake_category_id' => 5]);
        Stand::find(2)->update(['max_aircraft_id' => $b738->id, 'wake_category_id' => 5]);
        Stand::find(3)->update(['max_aircraft_id' => $a330->id, 'wake_category_id' => 5]);

        $stands = Stand::appropriateDimensions($a330)->get()->pluck('id')->toArray();

        $this->assertEquals([1, 3], $stands);
    }

    public function testAppropriateWakeCategoryOnlyReturnsLargeEnoughStands()
    {
        $a330 = Aircraft::where('code', 'A333')->first();
        Stand::find(1)->update(['wake_category_id' => 6]);
        Stand::find(2)->update(['wake_category_id' => 4]);
        Stand::find(3)->update(['wake_category_id' => 5]);

        $stands = Stand::appropriateWakeCategory($a330)->get()->pluck('id')->toArray();

        $this->assertEquals([1, 3], $stands);
    }

    public function testSizeAppropriateOnlyReturnsStandsThatAreBigEnough()
    {
        $a330 = Aircraft::where('code', 'A333')->first();

        // Make this stand too small wake-wise
        Stand::find(2)->update(['wake_category_id' => 4]);

        // Make this stand too small by max aircraft size
        $b738 = Aircraft::where('code', 'B738')->first();
        $b738->update(['length' => 999.99]);

        // Make this stand allowable
        Stand::find(3)->update(['wake_category_id' => 5]);

        $stands = Stand::sizeAppropriate($a330)->get()->pluck('id')->toArray();

        $this->assertEquals([3], $stands);
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
}
