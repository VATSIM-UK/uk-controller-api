<?php

namespace App\Services\Metar\Parser;

use App\BaseUnitTestCase;
use App\Models\Airfield\Airfield;

class WindParserTest extends BaseUnitTestCase
{
    private WindParser $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = $this->app->make(WindParser::class);
    }

    /**
     * @dataProvider windDataProvider
     */
    public function testItParsesWind(string $windToken, int $expectedSpeed, int $expectedDirection, ?int $expectedGust)
    {
        $parsed = $this->parser->parse(new Airfield(), collect([$windToken]));
        $this->assertCount(3, $parsed);
        $this->assertEquals($expectedSpeed, $parsed->offsetGet('wind_speed'));
        $this->assertEquals($expectedDirection, $parsed->offsetGet('wind_direction'));
        $this->assertEquals($expectedGust, $parsed->offsetGet('wind_gust'));
    }

    public function windDataProvider(): array
    {
        return [
            'No gusts' => [
                '26025KT',
                25,
                260,
                null
            ],
            'No gusts heading below 90' => [
                '06025KT',
                25,
                60,
                null
            ],
            'No gusts over 100kt' => [
                '260125KT',
                125,
                260,
                null
            ],
            'With gusts' => [
                '26025G32KT',
                25,
                260,
                32
            ],
            'With gusts over 100kt' => [
                '26025G321KT',
                25,
                260,
                321
            ],
        ];
    }

    /**
     * @dataProvider badWindProvider
     */
    public function testItHandlesBadWind(string $windToken)
    {
        $parsed = $this->parser->parse(new Airfield(), collect([$windToken]));
        $this->assertEmpty($parsed);
    }

    public function badWindProvider(): array
    {
        return [
            'No knots' => [
                '26025',
            ],
            'Missing g for gust' => [
                '26025321KT',
            ],
            'No data' => [
                '0KT',
            ],
        ];
    }
}
