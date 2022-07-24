<?php
namespace App\Http\Middleware;

use App\BaseApiTestCase;
use TestingUtils\Traits\WithSeedUsers;

class AuthenticateTest extends BaseApiTestCase
{
    use WithSeedUsers;

    public function testItConstructs()
    {
        $this->assertInstanceOf(Authenticate::class, $this->app->make(Authenticate::class));
    }

    public function testItRejectsUsersWithNoKey()
    {
        $this->json('GET', '/api/authorise')->assertStatus(401);
    }

    public function testItRejectsUsersWithInvalidKey()
    {
        $this->json('GET', '/api/authorise', [], ['Authorization' => 'Bearer nope'])
            ->assertStatus(401);
    }

    public function testItRedirectsToLoginPageIfWebRequestNotLoggedIn()
    {
        $this->get('horizon')
            ->assertRedirect();
    }
}
