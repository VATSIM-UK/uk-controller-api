<?php

namespace App\Services\Metar\Parser;

use App\BaseUnitTestCase;
use App\Models\Airfield\Airfield;
use PHPUnit\Framework\Attributes\DataProvider;

class VisibilityParserTest extends BaseUnitTestCase
{
    private VisibilityParser $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = $this->app->make(VisibilityParser::class);
    }

    public function testItParsesVisibility()
    {
        $parsed = $this->parser->parse(new Airfield(), collect(['5000']));
        $this->assertCount(1, $parsed);
        $this->assertEquals(5000, $parsed->offsetGet('visibility'));
    }

    #[DataProvider('badVisibilityProvider')]
    public function testItDoesntParseMalformedVisibility(string $visibility)
    {
        $this->assertEmpty($this->parser->parse(new Airfield(), collect([$visibility])));
    }

    public static function badVisibilityProvider(): array
    {
        return [
            'Too long' => [
                '20000'
            ],
            'Too short' => [
                '200'
            ],
            'Non numeric' => [
                '200A'
            ],
            'Value before' => [
                'A2000'
            ],
            'Value after' => [
                '2000A'
            ],
        ];
    }
}
