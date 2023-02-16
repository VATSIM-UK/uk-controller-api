<?php

namespace App\Services\Metar\Parser;

use App\BaseUnitTestCase;
use App\Models\Airfield\Airfield;
use PHPUnit\Metadata\Api\DataProvider;

class WindVariationParserTest extends BaseUnitTestCase
{
    private WindVariationParser $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = $this->app->make(WindVariationParser::class);
    }

    public function testItParsesWindVariation()
    {
        $parsed = $this->parser->parse(new Airfield(), collect(['210V250']));
        $this->assertCount(1, $parsed);
        $this->assertEquals('210V250', $parsed->offsetGet('wind_variation'));
    }

    #[DataProvider('badWindProvider')]
    public function testItHandlesBadWindVariation(string $windToken)
    {
        $parsed = $this->parser->parse(new Airfield(), collect([$windToken]));
        $this->assertEmpty($parsed);
    }

    public static function badWindProvider(): array
    {
        return [
            'No V' => [
                '210250',
            ],
            'Too short left side' => [
                '21V250',
            ],
            'Too short right side' => [
                '210V25',
            ],
            'Too short both sides' => [
                '21V25',
            ],
            'Data before' => [
                'A210V250',
            ],
            'Data after' => [
                '210V250A',
            ],
        ];
    }
}
