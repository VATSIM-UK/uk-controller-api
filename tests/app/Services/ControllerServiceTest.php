<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Helpers\Vatsim\ParsedControllerPosition;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\ControllerPositionAlternativeCallsign;

class ControllerServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var ControllerService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(ControllerService::class);
    }

    /**
     * @dataProvider controllerLevelProvider
     */
    public function testItGetsControllerLevelFromCallsign(string $value, string $expected)
    {
        $this->assertEquals($expected, ControllerService::getControllerLevelFromCallsign($value));
    }

    public function controllerLevelProvider(): array
    {
        return [
            ['', ''],
            ['EGKK_DEL', 'DEL'],
            ['EGKK_GND', 'GND'],
            ['EGKK_TWR', 'TWR'],
            ['EGKK_APP', 'APP'],
            ['LON_S_CTR', 'CTR'],
            ['egkk_del', 'DEL'],
            ['egkk_gnd', 'GND'],
            ['egkk_twr', 'TWR'],
            ['egkk_app', 'APP'],
            ['lon_s_ctr', 'CTR'],
        ];
    }

    /**
     * @dataProvider controllerFacilityProvider
     */
    public function testItGetsControllerFacilityFromCallsign(string $value, string $expected)
    {
        $this->assertEquals($expected, ControllerService::getControllerFacilityFromCallsign($value));
    }

    public function controllerFacilityProvider(): array
    {
        return [
            ['EGKK', 'EGKK'],
            ['EGKK_DEL', 'EGKK'],
            ['EGKK_TWR', 'EGKK'],
            ['SCO_CTR', 'SCO'],
            ['LTC_CTR', 'LTC'],
            ['STC_1_CTR', 'STC'],
            ['LON_S_CTR', 'LON'],
            ['egkk', 'EGKK'],
            ['egkk_del', 'EGKK'],
            ['egkk_twr', 'EGKK'],
            ['sco_ctr', 'SCO'],
            ['ltc_ctr', 'LTC'],
            ['stc_ctr', 'STC'],
            ['lon_s_ctr', 'LON'],
        ];
    }

    public function testItCreatesControllerPositionsDependency()
    {
        $positionWithNoTopDown = ControllerPosition::factory()->create();
        $expected = [
            [
                'id' => 1,
                'callsign' => 'EGLL_S_TWR',
                'frequency' => 118.5,
                'top_down' => [
                    'EGLL',
                ],
                'requests_departure_releases' => true,
                'receives_departure_releases' => false,
                'sends_prenotes' => true,
                'receives_prenotes' => false,
            ],
            [
                'id' => 2,
                'callsign' => 'EGLL_N_APP',
                'frequency' => 119.72,
                'top_down' => [
                    'EGLL',
                ],
                'requests_departure_releases' => true,
                'receives_departure_releases' => true,
                'sends_prenotes' => true,
                'receives_prenotes' => true,
            ],
            [
                'id' => 3,
                'callsign' => 'LON_S_CTR',
                'frequency' => 129.42,
                'top_down' => [
                    'EGLL',
                ],
                'requests_departure_releases' => true,
                'receives_departure_releases' => true,
                'sends_prenotes' => true,
                'receives_prenotes' => true,
            ],
            [
                'id' => 4,
                'callsign' => 'LON_C_CTR',
                'frequency' => 127.1,
                'top_down' => [
                    'EGBB',
                ],
                'requests_departure_releases' => false,
                'receives_departure_releases' => false,
                'sends_prenotes' => false,
                'receives_prenotes' => false,
            ],
            [
                'id' => $positionWithNoTopDown->id,
                'callsign' => $positionWithNoTopDown->callsign,
                'frequency' => $positionWithNoTopDown->frequency,
                'top_down' => [],
                'requests_departure_releases' => false,
                'receives_departure_releases' => false,
                'sends_prenotes' => false,
                'receives_prenotes' => false,
            ],
        ];

        $actual = $this->service->getControllerPositionsDependency()->toArray();
        $this->assertSame($expected, $actual);
    }

    public function testItReturnsParsedControllerPositions()
    {
        // Add an "alternative callsign for LON_C_CTR, which should show up
        ControllerPositionAlternativeCallsign::create(['controller_position_id' => 4, 'callsign' => 'LCC_CTR']);

        // Add a bogus callsign to test
        ControllerPosition::create(['callsign' => 'FOO', 'frequency' => 199.998]);

        $expected = collect([]);
        $expected->put(
            1,
            collect([new ParsedControllerPosition('EGLL', 'TWR', 118.5)])
        );
        $expected->put(
            2,
            collect([new ParsedControllerPosition('EGLL', 'APP', 119.72)])
        );
        $expected->put(
            3,
            collect([new ParsedControllerPosition('LON', 'CTR', 129.42)])
        );
        $expected->put(
            4,
            collect([new ParsedControllerPosition('LON', 'CTR', 127.1), new ParsedControllerPosition('LCC', 'CTR', 127.1)])
        );

        $this->assertEquals($expected, $this->service->getParsedControllerPositionsWithAlternatives());
    }
}
