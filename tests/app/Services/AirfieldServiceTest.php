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
                "id" => 1,
                "code" => "EGLL",
                "transition_altitude" => 6000,
                "controllers" => [
                    1,
                    2,
                    3,
                ],
            ],
            [
                "id" => 2,
                "code" => "EGBB",
                "transition_altitude" => 6000,
                "controllers" => [
                    4,
                ],
            ],
            [
                "id" => 3,
                "code" => "EGKR",
                "transition_altitude" => 6000,
                "controllers" => [],
            ],
        ];

        $actual = $this->service->getAllAirfieldsWithTopDown();
        $this->assertEquals($expected, $actual);
    }
}
