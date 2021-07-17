<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Providers\AuthServiceProvider;
use App\Services\SidService;
use Illuminate\Support\Facades\Cache;

class SidControllerTest extends BaseApiTestCase
{
    const SID_URI_1 = 'sid/1';
    const SID_URI_55 = 'sid/55';

    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_USER,
        AuthServiceProvider::SCOPE_DEPENDENCY_ADMIN,
    ];

    public function testItConstructs()
    {
        $this->assertInstanceOf(SidController::class, $this->app->make(SidController::class));
    }

    public function testItReturnsHandoffData()
    {
        $expected = [
            'EGLL' => [
                'TEST1X' => 'HANDOFF_ORDER_1',
                'TEST1Y' => 'HANDOFF_ORDER_1',
            ],
            'EGBB' => [
                'TEST1A' => 'HANDOFF_ORDER_2',
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'handoffs')->assertJson($expected);
    }

    public function testItReturnsASid()
    {
        $expected = [
            'id' => 1,
            'identifier' => 'TEST1X',
            'airfield_id' => 1,
            'initial_altitude' => 3000,
            'handoff_id' => 1,
        ];
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SID_URI_1)->assertJson($expected);
    }

    public function testItReturns200OnGetSid()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, self::SID_URI_1)->assertStatus(200);
    }

    public function testItReturns404OnGetSidNotFound()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, self::SID_URI_55)->assertStatus(404);
    }

    public function testItReturnsAllSids()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'sid')->assertStatus(200)
            ->assertExactJson($this->app->make(SidService::class)->getAllSids());
    }

    public function testItReturns200OnGetAllSids()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'sid')->assertStatus(200);
    }

    public function testDeletingSidReturns204OnSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, self::SID_URI_1)->assertStatus(204);
    }

    public function testDeletingSidReturns404OnNotFound()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, self::SID_URI_55)->assertStatus(404);
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
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::SID_URI_1, $data);
        $this->assertDatabaseHas('sid', $data);
    }

    public function testItReturnsOkOnSidUpdate()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 1,
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::SID_URI_1, $data)->assertStatus(200);
    }

    public function testItFailsSidUpdateMissingIdentifier()
    {
        $data = [
            'notidentifier' => 'TEST1U',
            'airfield_id' => 1,
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::SID_URI_1, $data)->assertStatus(400);
    }

    public function testItFailsSidUpdateMissingAirfield()
    {
        $data = [
            'identifier' => 'TEST1U',
            'notairfield_id' => 1,
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::SID_URI_1, $data)->assertStatus(400);
    }

    public function testItFailsSidUpdateAirfieldIdNotInteger()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 'test',
            'initial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::SID_URI_1, $data)->assertStatus(400);
    }

    public function testItFailsSidUpdateMissingInitialAltitude()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 1,
            'notinitial_altitude' => 10000,
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::SID_URI_1, $data)->assertStatus(400);
    }

    public function testItFailsSidUpdateInitialAltitudeNotInteger()
    {
        $data = [
            'identifier' => 'TEST1U',
            'airfield_id' => 1,
            'initial_altitude' => 'test',
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::SID_URI_1, $data)->assertStatus(400);
    }

    public function testItReturnsSidsDependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'sid/dependency')
            ->assertOk()
            ->assertJson($this->app->make(SidService::class)->getSidsDependency());
    }
}
