<?php

namespace App\Models\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Vatsim\NetworkAircraft;

class StandTest extends BaseFunctionalTestCase
{
    public function testUnoccupiedOnlyReturnsFreeStands()
    {
        NetworkAircraft::find('BAW123')->occupiedStand()->sync([2]);
        $stands = Stand::unoccupied()->get()->pluck('id')->toArray();
        $this->assertEquals([1, 3], $stands);
    }
}
