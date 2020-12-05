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

    /**
     * @dataProvider aircraftTypeProvider
     */
    public function testItReturnsCorrectAircraftTypeString(?string $rawType, string $expectedType)
    {
        $aircraft = new NetworkAircraft(['planned_aircraft' => $rawType]);
        $this->assertEquals($expectedType, $aircraft->aircraftType);
    }

    public function aircraftTypeProvider(): array
    {
        return [
            'No separators 1' => [
                'B738',
                'B738',
            ],
            'No separators 2' => [
                'A343',
                'A343',
            ],
            'Single separator 1' => [
                'B738/L',
                'B738',
            ],
            'Single separator 2' => [
                'B744/ABCDEFGH',
                'B744',
            ],
            'Double separator 1' => [
                'ABC/B744/DEFGH',
                'B744',
            ],
            'Double separator 2' => [
                'fgfg/CONC/asdsadfdsfgfdghdfgfdg',
                'CONC',
            ],
            'Extra separators' => [
                'fgfg/CONC/asdsadf/dsfgfdghdfg/fdg/',
                'CONC',
            ],
            'No type at all' => [
                null,
                ''
            ],
            'Vatsim prefile equipment' => [
                'B738/M-SDE2E3FGHIRWXY/LB1',
                'B738',
            ],
            'Bad order' => [
                'H/B738',
                'B738',
            ],
            'Complete rubbish' => [
                'asdsadsadsadas',
                ''
            ],
        ];
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
