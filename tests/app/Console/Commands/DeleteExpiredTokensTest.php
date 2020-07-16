<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Laravel\Passport\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class DeleteExpiredTokensTest extends BaseFunctionalTestCase
{
    const SCOPES = '"[user]"';
    
    public function testItDeletesExpiredTokens()
    {
        Token::create(
            [
                'id' => '1',
                'user_id' => self::ACTIVE_USER_CID,
                'client_id' => 1,
                'name' => 'access',
                'scopes' => self::SCOPES,
                'revoked' => 0,
                'expires_at' => Carbon::now()->addSecond(1),
            ]
        );
        Token::create(
            [
                'id' => '2',
                'user_id' => self::ACTIVE_USER_CID,
                'client_id' => 1,
                'name' => 'access',
                'scopes' => self::SCOPES,
                'revoked' => 0,
                'expires_at' => Carbon::now()->addDays(1),
            ]
        );
        Token::create(
            [
                'id' => '3',
                'user_id' => self::ACTIVE_USER_CID,
                'client_id' => 1,
                'name' => 'access',
                'scopes' => self::SCOPES,
                'revoked' => 0,
                'expires_at' => Carbon::now()->subHour(1),
            ]
        );

        // Check tokens are in the database
        $this->assertDatabaseHas('oauth_access_tokens', ['id' => '1']);
        $this->assertDatabaseHas('oauth_access_tokens', ['id' => '2']);
        $this->assertDatabaseHas('oauth_access_tokens', ['id' => '3']);

        Artisan::call('tokens:delete-expired');
        $this->assertDatabaseHas('oauth_access_tokens', ['id' => '1']);
        $this->assertDatabaseHas('oauth_access_tokens', ['id' => '2']);
        $this->assertDatabaseMissing('oauth_access_tokens', ['id' => '3']);
    }
}
