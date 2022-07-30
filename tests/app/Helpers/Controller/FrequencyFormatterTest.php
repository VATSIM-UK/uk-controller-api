<?php

namespace App\Helpers\Controller;

use App\BaseUnitTestCase;

class FrequencyFormatterTest extends BaseUnitTestCase
{
    /**
     * @dataProvider frequencyProvider
     */
    public function testItFormatsFrequencies(float $frequency, string $expected)
    {
        $this->assertEquals(
            $expected,
            FrequencyFormatter::formatFrequency($frequency)
        );
    }

    public function frequencyProvider(): array
    {
        return [
            '6 digits' => [123.456, '123.456'],
            '5 digits' => [123.45, '123.450'],
            '4 digits' => [123.4, '123.400'],
            '3 digits' => [123, '123.000'],
        ];
    }
}
