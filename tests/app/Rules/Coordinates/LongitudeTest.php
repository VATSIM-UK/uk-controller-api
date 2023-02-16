<?php

namespace App\Rules\Coordinates;

use App\BaseUnitTestCase;
use PHPUnit\Metadata\Api\DataProvider;

class LongitudeTest extends BaseUnitTestCase
{
    private Longitude $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = new Longitude();
    }

    #[DataProvider('goodDataProvider')]
    public function testItPasses($value)
    {
        $this->assertTrue($this->rule->passes('', $value));
    }

    public static function goodDataProvider(): array
    {
        return [
            'Valid float' => ['45.1'],
            'Valid negative float' => ['-45.1'],
            'Valid int' => ['45'],
            'Valid negative int' => ['-45'],
            'Valid float numeric type' => [45.1],
            'Valid negative float numeric type' => [-45.1],
            'Valid int numeric type' => [45],
            'Valid negative int numeric type' => [-45],
            'Valid float upper bound' => ['180.0'],
            'Valid int upper bound' => ['180'],
            'Valid float lower bound' => ['-180.0'],
            'Valid int lower bound' => ['-180'],
            'Valid float close upper bound' => ['179.9'],
            'Valid int close upper bound' => ['179'],
            'Valid float close lower bound' => ['-179.9'],
            'Valid int close lower bound' => ['-179'],
        ];
    }

    #[DataProvider('badDataProvider')]
    public function testItFails($value)
    {
        $this->assertFalse($this->rule->passes('', $value));
    }

    public static function badDataProvider(): array
    {
        return [
            'Is null' => [null],
            'Is not numeric' => ['123.a'],
            'Below lower bound float' => ['-180.1'],
            'Below lower bound int' => ['-181'],
            'Below lower bound float numeric type' => [-180.1],
            'Below lower bound int numeric type' => [-181],
            'Above lower bound float' => ['180.1'],
            'Above lower bound int' => ['181'],
            'Above lower bound float numeric type' => [180.1],
            'Above lower bound int numeric type' => [181],
        ];
    }

    public function testItHasMessage()
    {
        $this->assertEquals('Invalid coordinate longitude', $this->rule->message());
    }
}
