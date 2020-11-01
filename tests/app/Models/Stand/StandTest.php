<?php

namespace App\Models\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Airline\Airline;
use App\Models\Vatsim\NetworkAircraft;

class StandTest extends BaseFunctionalTestCase
{
    public function testUnoccupiedOnlyReturnsFreeStands()
    {
        NetworkAircraft::find('BAW123')->occupiedStand()->sync([2]);
        $stands = Stand::unoccupied()->get()->pluck('id')->toArray();
        $this->assertEquals([1, 3], $stands);
    }

    public function testUnassignedOnlyReturnsUnassignedStands()
    {
        StandAssignment::create(['callsign' => 'BAW123', 'stand_id' => 2]);

        $stands = Stand::unassigned()->get()->pluck('id')->toArray();
        $this->assertEquals([1, 3], $stands);
    }

    public function testAvailableOnlyReturnsUnassignedUnoccupiedStands()
    {
        NetworkAircraft::find('BAW123')->occupiedStand()->sync([1]);
        StandAssignment::create(['callsign' => 'BAW123', 'stand_id' => 2]);

        $stands = Stand::available()->get()->pluck('id')->toArray();
        $this->assertEquals([3], $stands);
    }

    public function testAirlineOnlyReturnsStandsForThatAirline()
    {
        Airline::where('icao_code', 'BAW')->first()->stands()->sync([1, 3]);
        $stands = Stand::airline(Airline::where('icao_code', 'BAW')->first())->get()->pluck('id')->toArray();
        $this->assertEquals([1, 3], $stands);
    }
}
