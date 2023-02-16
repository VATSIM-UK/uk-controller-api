<?php

namespace App\Rules\Controller;

use App\BaseUnitTestCase;
use PHPUnit\Metadata\Api\DataProvider;

class ControllerPositionCallsignTest extends BaseUnitTestCase
{
    #[DataProvider('callsignProvider')]
    public function testItValidatesCallsign(mixed $callsign, bool $expected)
    {
        $this->assertEquals($expected, (new ControllerPositionCallsign())->passes('callsign', $callsign));
    }

    public static function callsignProvider(): array
    {
        return [
            'Null' => [null, false],
            'Numeric' => [123, false],
            'No underscores' => ['EGLLTWR', false],
            'Hyphens' => ['EGLL-TWR', false],
            'Invalid prefix' => ['EG12_TWR', false],
            'Invalid suffix' => ['EGLL_OBS', false],
            'Lowercase' => ['egll_del', false],
            'Space before' => [' EGLL_DEL', false],
            'Space after' => ['EGLL_DEL ', false],
            'Middle of string' => ['hi EGLL_DEL how are you', false],
            'Delivery' => ['EGLL_DEL', true],
            'Ground' => ['EGLL_GND', true],
            'Tower' => ['EGLL_TWR', true],
            'Approach' => ['EGLL_APP', true],
            'Enroute' => ['EGLL_CTR', true],
            'FSS' => ['EGLL_FSS', true],
            'Delivery Middle Letter' => ['EGLL_A_DEL', true],
            'Ground Middle Letter' => ['EGLL_A_GND', true],
            'Tower Middle Letter' => ['EGLL_A_TWR', true],
            'Approach Middle Letter' => ['EGLL_A_APP', true],
            'Enroute Middle Letter' => ['EGLL_A_CTR', true],
            'FSS Middle Letter' => ['EGLL_A_FSS', true],
            'Delivery Middle Number' => ['EGLL_1_DEL', true],
            'Ground Middle Number' => ['EGLL_1_GND', true],
            'Tower Middle Number' => ['EGLL_1_TWR', true],
            'Approach Middle Number' => ['EGLL_1_APP', true],
            'Enroute Middle Number' => ['EGLL_1_CTR', true],
            'FSS Middle Number' => ['EGLL_1_FSS', true],
        ];
    }
}
