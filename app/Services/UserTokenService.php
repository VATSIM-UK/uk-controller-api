<?php

namespace App\Services;

use App\Models\User\User;
use App\Providers\AuthServiceProvider;
use App\Exceptions\TooManyTokensException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Passport\Token;

class UserTokenService
{
    // The maximum allowed access tokens that an individual user may have
    const MAXIMUM_ALLOWED_TOKENS = 4;

    /**
     * Creates an access token for the given user
     *
     * @param integer $userCid
     * @throws ModelNotFoundException
     * @throws TooManyTokensException
     * @return string
     */
    public function create(int $userCid) : string
    {
        $user = User::findOrFail($userCid);

        if ($user->tokens->count() >= self::MAXIMUM_ALLOWED_TOKENS) {
            throw new TooManyTokensException('Too many tokens created for user');
        }

        return $user->createToken('access', [AuthServiceProvider::SCOPE_USER])->accessToken;
    }

    /**
     * Deletes the given token.
     *
     * @param string $tokenId
     * @return bool
     */
    public function delete(string $tokenId) : bool
    {
        $token = Token::findOrFail($tokenId);
        return $token->delete();
    }

    /**
     * Delete all the tokens for a given user
     *
     * @param integer $userCid
     * @return void
     */
    public function deleteAllForUser(int $userCid)
    {
        $tokens = User::findOrFail($userCid)->tokens;
        foreach ($tokens as $token) {
            $token->delete();
        }
    }
}
