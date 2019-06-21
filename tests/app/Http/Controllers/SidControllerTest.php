<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\SidService;
use Illuminate\Support\Facades\Cache;

class SidControllerTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::forget(SidService::DEPENDENCY_CACHE_KEY);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(SidController::class, $this->app->make(SidController::class));
    }

    public function testItReturns200OnInitialAltitudeDataSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'initial-altitude')->assertStatus(200);
    }

    public function testItReturnsInitialAltitudeData()
    {
        $expected = [
            'EGLL' => [
                'TEST1X' => 3000,
                'TEST1Y' => 4000,
            ],
            'EGBB' => [
                'TEST1A' => 5000,
            ],
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'initial-altitude')->assertJson($expected);
    }
}
