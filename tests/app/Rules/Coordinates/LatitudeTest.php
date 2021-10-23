<?php

namespace App\Rules\Coordinates;

use App\BaseUnitTestCase;

class LatitudeTest extends BaseUnitTestCase
{
    private Latitude $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = new Latitude();
    }

    /**
     * @dataProvider goodDataProvider
     */
    public function testItPasses($value)
    {
        $this->assertTrue($this->rule->passes('', $value));
    }

    public function goodDataProvider(): array
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
            'Valid float upper bound' => ['90.0'],
            'Valid int upper bound' => ['90'],
            'Valid float lower bound' => ['-90.0'],
            'Valid int lower bound' => ['-90'],
            'Valid float close upper bound' => ['89.9'],
            'Valid int close upper bound' => ['89'],
            'Valid float close lower bound' => ['-89.9'],
            'Valid int close lower bound' => ['-89'],
        ];
    }

    /**
     * @dataProvider badDataProvider
     */
    public function testItFails($value)
    {
        $this->assertFalse($this->rule->passes('', $value));
    }

    public function badDataProvider(): array
    {
        return [
            'Is null' => [null],
            'Is not numeric' => ['123.a'],
            'Below lower bound float' => ['-90.1'],
            'Below lower bound int' => ['-91'],
            'Below lower bound float numeric type' => [-90.1],
            'Below lower bound int numeric type' => [-91],
            'Above lower bound float' => ['90.1'],
            'Above lower bound int' => ['91'],
            'Above lower bound float numeric type' => [90.1],
            'Above lower bound int numeric type' => [91],
        ];
    }

    public function testItHasMessage()
    {
        $this->assertEquals('Invalid coordinate latitude', $this->rule->message());
    }
}
