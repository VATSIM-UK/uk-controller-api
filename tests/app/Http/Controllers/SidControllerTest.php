<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Providers\AuthServiceProvider;
use App\Services\SidService;
use Illuminate\Support\Facades\Cache;

class SidControllerTest extends BaseApiTestCase
{
    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_USER,
        AuthServiceProvider::SCOPE_DEPENDENCY_ADMIN,
    ];

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
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'initial-altitude')->assertStatus(200);
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

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'initial-altitude')->assertJson($expected);
    }

    public function testItReturnsASid()
    {
        $expected = [
            'id' => 1,
            'identifier' => 'TEST1X',
            'airfield_id' => 1,
            'initial_altitude' => 3000,
        ];
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'sid/1')->assertJson($expected);
    }

    public function testItReturns200OnGetSid()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'sid/1')->assertStatus(200);
    }

    public function testItReturns404OnGetSidNotFound()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'sid/55')->assertStatus(404);
    }

    public function testItReturnsAllSids()
    {
        $expected = [
            [
                'id' => 1,
                'identifier' => 'TEST1X',
                'airfield_id' => 1,
                'initial_altitude' => 3000,
            ],
            [
                'id' => 2,
                'identifier' => 'TEST1Y',
                'airfield_id' => 1,
                'initial_altitude' => 4000,
            ],
            [
                'id' => 3,
                'identifier' => 'TEST1A',
                'airfield_id' => 2,
                'initial_altitude' => 5000,
            ],
        ];
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'sid')->assertJson($expected);
    }

    public function testItReturns200OnGetAllSids()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'sid')->assertStatus(200);
    }

    public function testDeletingSidReturns204OnSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'sid/1')->assertStatus(204);
    }

    public function testDeletingSidReturns404OnNotFound()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'sid/55')->assertStatus(404);
    }

    public function testItCreatesASid()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 1,
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid', $data);
        $this->assertDatabaseHas('sid', $data);
    }

    public function testItReturnsCreatedOnSidCreation()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 1,
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid', $data)->assertStatus(201);
    }

    public function testItFailsSidCreationMissingIdentifier()
    {
        $data = [
            'notidentifier' => 'TEST1U',
            'airfield_id' => 1,
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid', $data)->assertStatus(400);
    }

    public function testItFailsSidCreationMissingAirfield()
    {
        $data = [
            'identifier' => 'TEST1U',
            'notairfield_id' => 1,
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid', $data)->assertStatus(400);
    }

    public function testItFailsSidCreationAirfieldIdNotInteger()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 'test',
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid', $data)->assertStatus(400);
    }

    public function testItFailsSidCreationMissingInitialAltitude()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 1,
            'notinitial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid', $data)->assertStatus(400);
    }

    public function testItFailsSidCreationInitialAltitudeNotInteger()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 1,
            'initial_altitude' => 'test',
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid', $data)->assertStatus(400);
    }

    public function testItUpdatesASid()
    {
        $data = [
            'id' => 1,
            'identifier' => 'TEST1U',
            'airfield_id' => 1,
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid/1', $data);
        $this->assertDatabaseHas('sid', $data);
    }

    public function testItReturnsOkOnSidUpdate()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 1,
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid/1', $data)->assertStatus(200);
    }

    public function testItFailsSidUpdateMissingIdentifier()
    {
        $data = [
            'notidentifier' => 'TEST1U',
            'airfield_id' => 1,
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid/1', $data)->assertStatus(400);
    }

    public function testItFailsSidUpdateMissingAirfield()
    {
        $data = [
            'identifier' => 'TEST1U',
            'notairfield_id' => 1,
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid/1', $data)->assertStatus(400);
    }

    public function testItFailsSidUpdateAirfieldIdNotInteger()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 'test',
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid/1', $data)->assertStatus(400);
    }

    public function testItFailsSidUpdateMissingInitialAltitude()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 1,
            'notinitial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid/1', $data)->assertStatus(400);
    }

    public function testItFailsSidUpdateInitialAltitudeNotInteger()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 1,
            'initial_altitude' => 'test',
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'sid/1', $data)->assertStatus(400);
    }
}
