<?php

namespace App\Models\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Airline\Airline;
use App\Models\Vatsim\NetworkAircraft;
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

    public function testAvailableOnlyReturnsUnassignedUnoccupiedStands()
    {
        NetworkAircraft::find('BAW123')->occupiedStand()->sync([1]);
        StandAssignment::create(['callsign' => 'BAW123', 'stand_id' => 2]);

        $stands = Stand::available()->get()->pluck('id')->toArray();
        $this->assertEquals([3], $stands);
    }

    public function testAirlineDestinationOnlyReturnsStandsForTheCorrectDestinations()
    {
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
}
