<?php

namespace TestingUtils\Traits;

use App\BaseTestCase;
use App\Models\User\User;

trait WithSeedUsers
{
    /**
     * @return User
     */
    public function activeUser() : User
    {
        return User::find(BaseTestCase::ACTIVE_USER_CID);
    }

    /**
     * @return User
     */
    public function bannedUser() : User
    {
        return User::find(BaseTestCase::BANNED_USER_CID);
    }

    /**
     * @return User
     */
    public function disabledUser() : User
    {
        return User::find(BaseTestCase::DISABLED_USER_CID);
    }
}
