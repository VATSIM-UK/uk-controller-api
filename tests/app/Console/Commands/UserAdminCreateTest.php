<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\User\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\PersonalAccessClient;

class UserAdminCreateTest extends BaseFunctionalTestCase
{
    const ARTISAN_COMMAND = 'user:create-admin';
    
    public function testItConstructs()
    {
        $instance = new UserAdminCreate();
        $this->assertInstanceOf(UserAdminCreate::class, $instance);
    }

    public function testItReturnsSuccess()
    {
        $this->assertEquals(0, Artisan::call(self::ARTISAN_COMMAND));
    }

    public function testItCreatesAUser()
    {
        $personalAccessClientId = PersonalAccessClient::latest()->firstOrFail()->client_id;

        Artisan::call(self::ARTISAN_COMMAND);
        $userId = User::where('id', 2)->first()->id;
        $this->assertDatabaseHas(
            'oauth_access_tokens',
            [
                'user_id' => $userId,
                'client_id' => $personalAccessClientId,
                'revoked' => 0,
            ]
        );
    }

    public function testItCreatesAToken()
    {
        Artisan::call(self::ARTISAN_COMMAND);
        $token = explode(PHP_EOL, Artisan::output())[1];
        $this->get('/api/useradmin', ['Authorization' => 'Bearer ' . $token])->assertStatus(200);
    }
}
