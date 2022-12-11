<?php

namespace App\Models\IntentionCode;

use App\BaseFunctionalTestCase;
use App\Services\IntentionCode\FirExitPointService;

class FirExitPointServiceTest extends BaseFunctionalTestCase
{
    private readonly FirExitPointService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(FirExitPointService::class);
    }

    public function testItReturnsDependency()
    {
        $point1 = FirExitPoint::create(
            [
                'exit_point' => 'FOO',
                'internal' => true,
                'exit_direction_start' => 123,
                'exit_direction_end' => 234,
            ]
        );

        $point2 = FirExitPoint::create(
            [
                'exit_point' => 'BAR',
                'internal' => false,
                'exit_direction_start' => 345,
                'exit_direction_end' => 123,
            ]
        );

        $expected = [
            [
                'id' => $point1->id,
                'exit_point' => 'FOO',
                'internal' => true,
                'exit_direction_start' => 123,
                'exit_direction_end' => 234,
            ],
            [
                'id' => $point2->id,
                'exit_point' => 'BAR',
                'internal' => false,
                'exit_direction_start' => 345,
                'exit_direction_end' => 123,
            ],
        ];

        $this->assertEquals($expected, $this->service->getFirExitDependency());
    }
}
