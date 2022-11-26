<?php

namespace App\Rules\Airfield;

use App\BaseUnitTestCase;

class AirfieldIcaoTest extends BaseUnitTestCase
{
    private readonly AirfieldIcao $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = $this->app->make(AirfieldIcao::class);
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
            'Too short' => ['EGL'],
            'Is null' => [null],
            'Too long' => ['EGCCC'],
            'Wrong type' => [123],
            'Lower case' => ['egcc'],
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
