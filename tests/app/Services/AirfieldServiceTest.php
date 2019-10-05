<?php

namespace App\Services;

use App\BaseFunctionalTestCase;

class AirfieldServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var AirfieldService
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(AirfieldService::class);
    }

    public function testItReturnsAirfieldsWithControllers()
    {
        $expected = [
            [
                'id' => 1,
                'code' => 'EGLL',
                'transition_altitude' => 6000,
                'controllers' => [
                    1,
                    2,
                    3,
                ],
                'pairing-prenotes' => [
                    2 => [
                        1,
                    ],
                ],
            ],
            [
                'id' => 2,
                'code' => 'EGBB',
                'transition_altitude' => 6000,
                'controllers' => [
                    4,
                ],
                'pairing-prenotes' => [],
            ],
            [
                'id' => 3,
                'code' => 'EGKR',
                'transition_altitude' => 6000,
                'controllers' => [],
                'pairing-prenotes' => [],
            ],
        ];

        $actual = $this->service->getAllAirfieldsWithRelations();
        $this->assertEquals($expected, $actual);
    }
}
