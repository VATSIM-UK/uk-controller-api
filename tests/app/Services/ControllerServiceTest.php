<?php

namespace App\Services;

use App\BaseFunctionalTestCase;

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

    public function testItCreatesLegacyControllerPositionsDependency()
    {
        $expected = [
            'EGLL_S_TWR' => [
                'frequency' => 118.5,
                'top-down' => [
                    'EGLL',
                ],
            ],
            'EGLL_N_APP' => [
                'frequency' => 119.72,
                'top-down' => [
                    'EGLL',
                ],
            ],
            'LON_S_CTR' => [
                'frequency' => 129.42,
                'top-down' => [
                    'EGLL',
                ],
            ],
            'LON_C_CTR' => [
                'frequency' => 127.1,
                'top-down' => [
                    'EGBB',
                ],
            ],
        ];

        $actual = $this->service->getLegacyControllerPositionsDependency();
        $this->assertSame($expected, $actual);
    }

    public function testItCreatesLegacyAirfieldOwnershipDependency()
    {
        $expected = [
            'EGLL' => [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
            ],
            'EGBB' => [
                'LON_C_CTR',
            ],
        ];

        $actual = $this->service->getLegacyAirfieldOwnershipDependency();
        $this->assertSame($expected, $actual);
    }
}
