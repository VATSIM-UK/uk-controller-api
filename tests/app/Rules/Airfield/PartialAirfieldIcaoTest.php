<?php

namespace App\Rules\Airfield;

use App\BaseUnitTestCase;

class PartialAirfieldIcaoTest extends BaseUnitTestCase
{
    private readonly PartialAirfieldIcao $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = $this->app->make(PartialAirfieldIcao::class);
    }

    public function validDataProvider(): array
    {
        return [
            ['EGLL'],
            ['EGGD'],
            ['EGCC'],
            ['LXGB'],
            ['LPPT'],
            ['KJFK'],
            ['EDDF'],
            ['EG12'],
            ['L'],
            ['LF'],
            ['LFR'],
            ['L123'],
            ['LF12'],
            ['LFR3'],
            ['LFRR'],
        ];
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testItPassesOnValidIcaos(string $icao)
    {
        $this->assertTrue($this->rule->passes(null, $icao));
    }

    public function invalidDataProvider(): array
    {
        return [
            'Too short' => [''],
            'Is null' => [null],
            'Too long' => ['EGCCC'],
            'Lower case' => ['egcc'],
            'Wrong type' => [123],
            'Contains non-alphanumerics' => ['KJ[K'],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testItFailsOnBadIcaos($icao)
    {
        $this->assertFalse($this->rule->passes(null, $icao));
    }
}
