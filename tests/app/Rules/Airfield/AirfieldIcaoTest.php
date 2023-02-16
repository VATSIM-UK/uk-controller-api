<?php

namespace App\Rules\Airfield;

use App\BaseUnitTestCase;
use PHPUnit\Metadata\Api\DataProvider;

class AirfieldIcaoTest extends BaseUnitTestCase
{
    private readonly AirfieldIcao $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = $this->app->make(AirfieldIcao::class);
    }

    public static function validDataProvider(): array
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

    #[DataProvider('validDataProvider')]
    public function testItPassesOnValidIcaos(string $icao)
    {
        $this->assertTrue($this->rule->passes(null, $icao));
    }

    public static function invalidDataProvider(): array
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

    #[DataProvider('invalidDataProvider')]
    public function testItFailsOnBadIcaos($icao)
    {
        $this->assertFalse($this->rule->passes(null, $icao));
    }
}
