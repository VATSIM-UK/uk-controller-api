<?php

namespace App\Services;

use App\BaseFunctionalTestCase;

class PrenoteServiceTest extends BaseFunctionalTestCase
{
    private readonly PrenoteService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(PrenoteService::class);
    }

    public function testItReturnsPrenotesV2Dependency()
    {
        $expected = [
            [
                'id' => 1,
                'description' => 'Prenote One',
                'controller_positions' => [
                    1,
                    2,
                ],
            ],
            [
                'id' => 2,
                'description' => 'Prenote Two',
                'controller_positions' => [
                    2,
                    3,
                ],
            ],
        ];

        $this->assertEquals($expected, $this->service->getPrenotesV2Dependency());
    }
}
