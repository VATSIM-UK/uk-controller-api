<?php

namespace App\Http\Middleware;

use App\BaseApiTestCase;
use App\Models\User\User;
use TestingUtils\Traits\WithSeedUsers;

class UserPluginVersionTest extends BaseApiTestCase
{
    use WithSeedUsers;

    public function testItConstructs()
    {
        $this->assertInstanceOf(UserPluginVersion::class, $this->app->make(UserPluginVersion::class));
    }

    public function testItSetsUserVersion()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'version/2.0.0/status')->assertStatus(200);
        $this->assertEquals(2, $this->activeUser()->last_version);
    }
}
