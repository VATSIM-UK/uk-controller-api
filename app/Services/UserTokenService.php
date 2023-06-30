<?php

namespace App\Services;

use App\Models\User\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Passport\Token;

class UserTokenService
{
    /**
     * Creates an access token for the given user.
     *
     * @param integer $userCid
     * @throws ModelNotFoundException
     * @return string
     */
    public function create(int $userCid) : string
    {
        $user = User::findOrFail($userCid);

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
