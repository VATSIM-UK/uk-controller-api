<?php

namespace App\Rules\Heading;

use App\BaseUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ValidHeadingTest extends BaseUnitTestCase
{
    private ValidHeading $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = new ValidHeading();
    }

    #[DataProvider('goodDataProvider')]
    public function testItPasses($data)
    {
        $this->assertTrue($this->rule->passes('', $data));
    }

    public static function goodDataProvider(): array
    {
        return [
            'North Zero' => [0],
            'North' => [360],
            'North East Close' => [351],
            'North East' => [45],
            'East' => [90],
            'South East' => [135],
            'South' => [180],
            'South West' => [225],
            'West' => [270],
            'North West' => [315],
            'North West Close' => [359],
            'String value' => ['322'],
        ];
    }

    #[DataProvider('badDataProvider')]
    public function testItFails($data)
    {
        $this->assertFalse($this->rule->passes('', $data));
    }

    public static function badDataProvider(): array
    {
        return [
            'Null' => [null],
            'String negative' => ['-1'],
            'Negative' => [-1],
            'Too big' => [361],
            'String too big' => ['361'],
            'Floating point' => ['300.1'],
            'Not numeric' => ['abc'],
            'Empty string' => [''],
        ];
    }
}
