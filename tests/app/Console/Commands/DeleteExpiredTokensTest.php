<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Laravel\Passport\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class DeleteExpiredTokensTest extends BaseFunctionalTestCase
{
    public function testItDeletesExpiredTokens()
    {
        Token::create(
            [
                'id' => '1',
                'user_id' => '1203533',
                'client_id' => 1,
                'name' => 'access',
                'scopes' => '"[user]"',
                'revoked' => 0,
                'expires_at' => Carbon::now()->addSecond(1),
            ]
        );
        Token::create(
            [
                'id' => '2',
                'user_id' => '1203533',
                'client_id' => 1,
                'name' => 'access',
                'scopes' => '"[user]"',
                'revoked' => 0,
                'expires_at' => Carbon::now()->addDays(1),
            ]
        );
        Token::create(
            [
                'id' => '3',
                'user_id' => '1203533',
                'client_id' => 1,
                'name' => 'access',
                'scopes' => '"[user]"',
                'revoked' => 0,
                'expires_at' => Carbon::now()->subHour(1),
            ]
        );

        // Check tokens are in the database
        $this->seeInDatabase('oauth_access_tokens', ['id' => '1']);
        $this->seeInDatabase('oauth_access_tokens', ['id' => '2']);
        $this->seeInDatabase('oauth_access_tokens', ['id' => '3']);

        Artisan::call('tokens:delete-expired');
        $this->seeInDatabase('oauth_access_tokens', ['id' => '1']);
        $this->seeInDatabase('oauth_access_tokens', ['id' => '2']);
        $this->notSeeInDatabase('oauth_access_tokens', ['id' => '3']);
    }
}
