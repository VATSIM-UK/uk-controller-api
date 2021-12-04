<?php

namespace App\Services\Metar\Parser;

use App\BaseUnitTestCase;
use App\Models\Airfield\Airfield;

class PressureParserTest extends BaseUnitTestCase
{
    private PressureParser $parser;
    private Airfield $airfield;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = $this->app->make(PressureParser::class);
        $this->airfield = new Airfield(['elevation' => 300]);
    }

    /**
     * @dataProvider badDataProvider
     */
    public function testItDoesntFindBadData(array $tokens)
    {
        $parsed = collect();
        $this->parser->parse($this->airfield, collect($tokens), $parsed);
        $this->assertEmpty($parsed);
    }

    public function badDataProvider(): array
    {
        return [
            'Empty metar' => [
                []
            ],
            'Random tokens' => [
                ['abc']
            ],
            'QNH has something at start' => [
                ['BQ001']
            ],
            'QNH has something at end' => [
                ['Q001A']
            ],
            'Altimeter has something at start' => [
                ['BA0001']
            ],
            'Altimeter has something at end' => [
                ['A0001B']
            ],
            'Altimeter too long' => [
                ['A10011']
            ],
            'Altimeter too short' => [
                ['A100']
            ],
            'QNH too short' => [
                ['Q100']
            ],
            'QNH too long' => [
                ['Q10001']
            ],
            'QNH not numeric' => [
                ['Q10a1']
            ],
            'Altimeter not numeric' => [
                ['A10a1']
            ],
        ];
    }

    public function testItParsesQnhFromMetarTokens()
    {
        $parsed = collect();
        $this->parser->parse($this->airfield, collect(['EGKK', 'Q1013']), $parsed);
        $this->assertCount(4, $parsed);
        $this->assertEquals(1013, $parsed['qnh']);
        $this->assertEquals(29.91, $parsed['altimeter']);
        $this->assertEquals(1003, $parsed['qfe']);
        $this->assertEquals(29.62, $parsed['qfe_inhg']);
    }

    public function testItParsesAltimeterFromMetarTokens()
    {
        $parsed = collect();
        $this->parser->parse($this->airfield, collect(['EGKK', 'A2992']), $parsed);
        $this->assertCount(4, $parsed);
        $this->assertEquals(1013, $parsed['qnh']);
        $this->assertEquals(29.92, $parsed['altimeter']);
        $this->assertEquals(1003, $parsed['qfe']);
        $this->assertEquals(29.62, $parsed['qfe_inhg']);
    }

    public function testItPrefersQnhFromMetarTokens()
    {
        $parsed = collect();
        $this->parser->parse($this->airfield, collect(['EGKK', 'Q1014', 'A2992']), $parsed);
        $this->assertCount(4, $parsed);
        $this->assertEquals(1014, $parsed['qnh']);
        $this->assertEquals(29.94, $parsed['altimeter']);
        $this->assertEquals(1004, $parsed['qfe']);
        $this->assertEquals(29.65, $parsed['qfe_inhg']);
    }
}
