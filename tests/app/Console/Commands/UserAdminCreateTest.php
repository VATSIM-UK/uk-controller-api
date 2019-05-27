<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\User\User;
use Illuminate\Support\Facades\Artisan;

class UserAdminCreateTest extends BaseFunctionalTestCase
{
    public function testItConstructs()
    {
        $instance = new UserAdminCreate();
        $this->assertInstanceOf(UserAdminCreate::class, $instance);
    }

    public function testItReturnsSuccess()
    {
        $this->assertEquals(0, Artisan::call('user:create-admin'));
    }

    public function testItCreatesAUser()
    {
        Artisan::call('user:create-admin');
        $userId = User::where('id', 2)->first()->id;
        $this->assertDatabaseHas(
            'oauth_access_tokens',
            [
                'user_id' => $userId,
                'client_id' => 1,
                'revoked' => 0,
            ]
        );
    }

    public function testItCreatesAToken()
    {
        Artisan::call('user:create-admin');
        $token = explode(PHP_EOL, Artisan::output())[1];
        $this->get('/useradmin', ['Authorization' => 'Bearer ' . $token])->assertStatus(418);
    }
}
