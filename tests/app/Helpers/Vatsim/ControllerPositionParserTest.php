<?php

namespace App\Helpers\Vatsim;

use App\BaseUnitTestCase;
use App\Models\Controller\ControllerPosition;
use PHPUnit\Framework\Attributes\DataProvider;

class ControllerPositionParserTest extends BaseUnitTestCase
{
    private ControllerPositionParser $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = $this->app->make(ControllerPositionParser::class);
    }

    #[DataProvider('validDataProvider')]
    public function testItParsesValidPositions(
        ControllerPosition $positionToParse,
        string $expectedFacility,
        string $expectedType,
        float $expectedFrequency
    )
    {
        $actual = $this->parser->parse($positionToParse);
        $this->assertEquals($expectedFacility, $actual->getFacility());
        $this->assertEquals($expectedType, $actual->getUnitType());
        $this->assertEquals($expectedFrequency, $actual->getFrequency());
    }

    public static function validDataProvider(): array
    {
        return [
            [
                new ControllerPosition(['callsign' => 'EGLL_S_TWR', 'frequency' => 118.500]),
                'EGLL',
                'TWR',
                118.500
            ],
            [
                new ControllerPosition(['callsign' => 'EGLL_TWR', 'frequency' => 118.700]),
                'EGLL',
                'TWR',
                118.700
            ],
            [
                new ControllerPosition(['callsign' => 'ESSEX_APP', 'frequency' => 111.111]),
                'ESSEX',
                'APP',
                111.111
            ],
            [
                new ControllerPosition(['callsign' => 'LON_S_CTR', 'frequency' => 129.420]),
                'LON',
                'CTR',
                129.420
            ],
            [
                new ControllerPosition(['callsign' => 'LON_S1_CTR', 'frequency' => 129.420]),
                'LON',
                'CTR',
                129.420
            ],
            [
                new ControllerPosition(['callsign' => 'EGLL-S-TWR', 'frequency' => 118.500]),
                'EGLL',
                'TWR',
                118.500
            ],
            [
                new ControllerPosition(['callsign' => 'EGLL_S-TWR', 'frequency' => 118.500]),
                'EGLL',
                'TWR',
                118.500
            ],
            [
                new ControllerPosition(['callsign' => 'EGLL-S_TWR', 'frequency' => 118.500]),
                'EGLL',
                'TWR',
                118.500
            ],
        ];
    }

    #[DataProvider('badDataProvider')]
    public function testItDoesntParseInvalidPositions(ControllerPosition $positionToParse)
    {
        $this->assertNull($this->parser->parse($positionToParse));
    }

    public static function badDataProvider(): array
    {
        return [
            [
                new ControllerPosition(['callsign' => 'EGLL', 'frequency' => 118.500]),
            ],
        ];
    }
}
