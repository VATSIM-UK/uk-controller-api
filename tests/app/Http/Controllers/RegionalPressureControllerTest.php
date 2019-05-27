<?php


namespace App\Http\Controllers;

use App\BaseApiTestCase;
use Illuminate\Support\Facades\Cache;

class RegionalPressureControllerTest extends BaseApiTestCase
{
    public function testItConstructs()
    {
        $controller = new RegionalPressureController();
        $this->assertInstanceOf(RegionalPressureController::class, $controller);
    }

    public function testItRejectsTokensWithoutUserScope()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'regional-pressure')
            ->assertStatus(403);
    }

    public function testItReturnsCachedPressures()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with(RegionalPressureController::RPS_CACHE_KEY, [])
            ->andReturn(['Toddington' => 1011]);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'regional-pressure')
            ->assertJson(
                [
                    'data' => [
                        'Toddington' => 1011,
                    ],
                ]
            )
            ->assertStatus(200);
    }

    public function testItReturnsFailureOnNonCachedPressures()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with(RegionalPressureController::RPS_CACHE_KEY, [])
            ->andReturn([]);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'regional-pressure')
            ->assertJson(
                [
                'data' => [],
                ]
            )
            ->assertStatus(503);
    }

    public function testItDoesntAcceptPost()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'regional-pressure')->assertStatus(405);
    }
}
