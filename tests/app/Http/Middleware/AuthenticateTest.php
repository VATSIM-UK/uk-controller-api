<?php

namespace App\Http\Middleware;

use App\BaseApiTestCase;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Support\Facades\Config;
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

    public function testTheUserCanAccessHorizonIfTheyAreWebTeam()
    {
        Config::set('app.env', 'production');
        $user = User::factory()->create();
        $user->roles()->sync([Role::idFromKey(RoleKeys::WEB_TEAM)]);

        $this->actingAs($user)
            ->get('horizon')
            ->assertOk();
    }

    public function testTheUserCanNotAccessHorizonIfTheyAreNotWebTeam()
    {
        Config::set('app.env', 'production');
        $user = User::factory()->create();
        $user->roles()->sync([Role::idFromKey(RoleKeys::OPERATIONS_TEAM)]);

        $this->actingAs($user)
            ->get('horizon')
            ->assertForbidden();
    }
}
