<?php

namespace App\Rules\Controller;

use App\BaseUnitTestCase;
use PHPUnit\Metadata\Api\DataProvider;

class ControllerPositionPartialCallsignTest extends BaseUnitTestCase
{
    #[DataProvider('callsignProvider')]
    public function testItValidatesCallsign(mixed $callsign, bool $expected)
    {
        $this->assertEquals($expected, (new ControllerPositionPartialCallsign())->passes('callsign', $callsign));
    }

    public static function callsignProvider(): array
    {
        return [
            'Null' => [null, false],
            'Numeric' => [123, false],
            'No underscores' => ['EGLLTWR', true],
            'Hyphens' => ['EGLL-TWR', false],
            'Invalid prefix' => ['EG12_TWR', false],
            'Invalid suffix' => ['EGLL_OBS', false],
            'Lowercase' => ['egll_del', false],
            'Space before' => [' EGLL_DEL', false],
            'Space after' => ['EGLL_DEL ', false],
            'Middle of string' => ['hi EGLL_DEL how are you', false],
            'Short' => ['EGP', true],
            'Delivery' => ['EGLL_DEL', true],
            'Ground' => ['EGLL_GND', true],
            'Tower' => ['EGLL_TWR', true],
            'Approach' => ['EGLL_APP', true],
            'Enroute' => ['EGLL_CTR', true],
            'FSS' => ['EGLL_FSS', true],
        ];
    }
}
