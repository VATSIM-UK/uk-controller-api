<?php

namespace App\Services;

use App\BaseApiTestCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserConfigServiceTest extends BaseApiTestCase
{
    /**
     * Service under test
     *
     * @var UserConfigService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = $this->app->make(UserConfigService::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(UserConfigService::class, $this->service);
    }

    public function testcreateThrowsExceptionIfUserNotFound()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->create(-1);
    }

    public function testcreateReturnsConfig()
    {
        $config = $this->service->create(1203533);
        $this->assertEquals(env('APP_URL'), $config->apiUrl());
        $this->assertNotNull($config->apiKey());
    }
}
