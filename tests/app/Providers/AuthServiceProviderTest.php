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
        Carbon::setTestNow(Carbon::now());
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

    public function testTokensExpireInADecade()
    {
        $this->assertEquals(
            Carbon::now()->addDecade(),
            Carbon::now()->add(Passport::$personalAccessTokensExpireIn)
        );
    }
}
