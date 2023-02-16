<?php

namespace App\Services\Metar\Parser;

use App\BaseUnitTestCase;
use App\Models\Airfield\Airfield;
use PHPUnit\Metadata\Api\DataProvider;

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

    #[DataProvider('badDataProvider')]
    public function testItDoesntFindBadData(array $tokens)
    {
        $parsed = $this->parser->parse($this->airfield, collect($tokens));
        $this->assertEmpty($parsed);
    }

    public static function badDataProvider(): array
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
            'qnh_inhg has something at start' => [
                ['BA0001']
            ],
            'qnh_inhg has something at end' => [
                ['A0001B']
            ],
            'qnh_inhg too long' => [
                ['A10011']
            ],
            'qnh_inhg too short' => [
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
            'qnh_inhg not numeric' => [
                ['A10a1']
            ],
        ];
    }

    public function testItParsesQnhFromMetarTokens()
    {
        $parsed = $this->parser->parse($this->airfield, collect(['EGKK', 'Q1013']));
        $this->assertCount(5, $parsed);
        $this->assertEquals(1013, $parsed['qnh']);
        $this->assertEquals(29.91, $parsed['qnh_inhg']);
        $this->assertEquals(1003, $parsed['qfe']);
        $this->assertEquals(29.62, $parsed['qfe_inhg']);
        $this->assertEquals('hpa', $parsed['pressure_format']);
    }

    public function testItParsesqnh_inhgFromMetarTokens()
    {
        $parsed = $this->parser->parse($this->airfield, collect(['EGKK', 'A2992']));
        $this->assertCount(5, $parsed);
        $this->assertEquals(1013, $parsed['qnh']);
        $this->assertEquals(29.92, $parsed['qnh_inhg']);
        $this->assertEquals(1003, $parsed['qfe']);
        $this->assertEquals(29.62, $parsed['qfe_inhg']);
        $this->assertEquals('inhg', $parsed['pressure_format']);
    }

    public function testItPrefersQnhFromMetarTokens()
    {
        $parsed = $this->parser->parse($this->airfield, collect(['EGKK', 'Q1014', 'A2992']));
        $this->assertCount(5, $parsed);
        $this->assertEquals(1014, $parsed['qnh']);
        $this->assertEquals(29.94, $parsed['qnh_inhg']);
        $this->assertEquals(1004, $parsed['qfe']);
        $this->assertEquals(29.65, $parsed['qfe_inhg']);
        $this->assertEquals('hpa', $parsed['pressure_format']);
    }
}
