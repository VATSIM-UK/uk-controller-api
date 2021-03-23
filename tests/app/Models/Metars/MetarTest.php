<?php

namespace App\Models\Metars;

use App\BaseFunctionalTestCase;

class MetarTest extends BaseFunctionalTestCase
{
    /**
     * @dataProvider
     */
    public function testItParsesTheQnhFromTheMetar(string $metar, ?int $expected)
    {
        $this->assertEquals($expected, (new Metar(['metar_string' => $metar]))->getQnh());
    }

    public function qnhProvider(): array
    {
        return [
            [
                'EGLL Q1001',
                1001,
            ],
            [
                'EGLL Q0985',
                985,
            ],
            [
                'EGLL A3003',
                null,
            ],
            [
                'EGLL',
                null,
            ],
            [
                'EGLL',
                null,
            ],
            [
                'EGLL Q11111',
                null,
            ],
        ];
    }
}
