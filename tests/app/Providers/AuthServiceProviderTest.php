<?php

namespace App\Providers;

use App\BaseFunctionalTestCase;
use Laravel\Passport\Passport;
use Illuminate\Support\Carbon;

class AuthServiceProviderTest extends BaseFunctionalTestCase
{
    /**
     * @var AuthServiceProvider
     */
    private $provider;

    public function setUp() : void
    {
        parent::setUp();
        $this->provider = new AuthServiceProvider($this->app);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(AuthServiceProvider::class, $this->provider);
    }

    public function testItSetsScopes()
    {
        $this->assertEquals(
            array_values(Passport::scopeIds()),
            array_keys(AuthServiceProvider::AUTH_SCOPES)
        );
    }

    public function testTokensExpireInOneYear()
    {
        Carbon::setTestNow(Carbon::now());
        $this->assertEquals(
            Carbon::now()->addYear()->startOfMinute(),
            Carbon::createFromTimestamp(Passport::$personalAccessTokensExpireAt->getTimestamp())->startOfMinute()
        );
    }
}
