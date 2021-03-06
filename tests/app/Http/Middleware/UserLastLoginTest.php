<?php

namespace App\Http\Middleware;

use App\BaseApiTestCase;
use Carbon\Carbon;
use TestingUtils\Traits\WithSeedUsers;

class UserLastLoginTest extends BaseApiTestCase
{
    use WithSeedUsers;

    public function testItConstructs()
    {
        $this->assertInstanceOf(UserLastLogin::class, $this->app->make(UserLastLogin::class));
    }

    public function testItSetsLastLoginTimeNeverLoggedIn()
    {
        Carbon::setTestNow(Carbon::now());
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, '/authorise');
        $this->assertEquals(Carbon::now(), $this->activeUser()->last_login);
    }

    public function testItSetsLastLoginIpNeverLoggedIn()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, '/authorise');
        $this->assertEquals('127.0.0.1', $this->activeUser()->last_login_ip);
    }

    public function testItDoesntSetIpIfRecentLogin()
    {
        $this->updateUser(Carbon::now()->subMinutes(30));
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, '/authorise');
        $this->assertEquals('192.168.0.1', $this->activeUser()->last_login_ip);
    }

    public function testItDoesntSetLoginTimeIfRecentLogin()
    {
        Carbon::setTestNow(Carbon::now());
        $loginTime = Carbon::now()->subMinutes(30);

        $this->updateUser($loginTime);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, '/authorise');
        $this->assertEquals($loginTime, $this->activeUser()->last_login);
    }

    public function testItSetsLoginIpIfLoginNotRecent()
    {
        Carbon::setTestNow(Carbon::now());
        $this->updateUser(Carbon::now()->subMinutes(61));

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, '/authorise');
        $this->assertEquals('127.0.0.1', $this->activeUser()->last_login_ip);
    }

    public function testItSetsSetLoginTimeIfLoginNotRecent()
    {
        Carbon::setTestNow(Carbon::now());
        $this->updateUser(Carbon::now()->subMinutes(61));

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, '/authorise');
        $this->assertEquals(Carbon::now(), $this->activeUser()->last_login);
    }

    private function updateUser(Carbon $lastLoginTime)
    {
        $user = $this->activeUser();
        $user->last_login_ip = '192.168.0.1';
        $user->last_login = $lastLoginTime;
        $user->save();
    }
}
