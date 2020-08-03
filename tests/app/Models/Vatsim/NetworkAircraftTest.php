<?php

namespace App\Models\Vatsim;

use App\BaseUnitTestCase;

class NetworkAircraftTest extends BaseUnitTestCase
{
    public function testItSquawksMayday()
    {
        $aircraft = new NetworkAircraft(['transponder' => '7700']);
        $this->assertTrue($aircraft->squawkingMayday());
    }

    public function testItSquawksRadioFailure()
    {
        $aircraft = new NetworkAircraft(['transponder' => '7600']);
        $this->assertTrue($aircraft->squawkingRadioFailure());
    }

    public function testItSquawksBannedSquawk()
    {
        $aircraft = new NetworkAircraft(['transponder' => '7500']);
        $this->assertTrue($aircraft->squawkingBannedSquawk());
    }
}
