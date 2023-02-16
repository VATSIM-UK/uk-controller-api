<?php

namespace App\Rules\Squawk;

use App\BaseUnitTestCase;
use PHPUnit\Metadata\Api\DataProvider;

class SquawkCodeTest extends BaseUnitTestCase
{
    private SqauwkCode $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = $this->app->make(SqauwkCode::class);
    }

    public static function validDataProvider(): array
    {
        return [
            ['0000'],
            ['7700'],
            ['1234'],
            ['4215'],
            ['6465'],
            ['7777'],
            ['5734'],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function testItPassesOnValidSquawk(string $squawk)
    {
        $this->assertTrue($this->rule->passes(null, $squawk));
    }

    public static function invalidDataProvider(): array
    {
        return [
            'Too short' => ['123'],
            'Is null' => [null],
            'Too long' => ['12345'],
            'Wrong type' => [123],
            'Contains letters' => ['123F'],
            'Contains non-alphanumerics' => ['12[3'],
        ];
    }

    #[DataProvider('invalidDataProvider')]
    public function testItFailsOnBadIcaos($icao)
    {
        $this->assertFalse($this->rule->passes(null, $icao));
    }
}
