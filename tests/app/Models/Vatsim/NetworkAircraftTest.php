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

    public function testItIsIfr()
    {
        $aircraft = new NetworkAircraft(['planned_flighttype' => 'I']);
        $this->assertTrue($aircraft->isIfr());
        $this->assertFalse($aircraft->isVfr());
    }

    public function testItIsVfr()
    {
        $aircraft = new NetworkAircraft(['planned_flighttype' => 'V']);
        $this->assertTrue($aircraft->isVfr());
        $this->assertFalse($aircraft->isIfr());
    }

    public function testItIsSvfr()
    {
        $aircraft = new NetworkAircraft(['planned_flighttype' => 'S']);
        $this->assertTrue($aircraft->isVfr());
        $this->assertFalse($aircraft->isIfr());
    }
}
