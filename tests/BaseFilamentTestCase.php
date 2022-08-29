<?php

namespace App;

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;

abstract class BaseFilamentTestCase extends BaseFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $user = $this->filamentUser();
        $user->roles()->sync([Role::idFromKey(RoleKeys::WEB_TEAM)]);
        $this->actingAs($user, 'web');
    }

    protected function filamentUser(): User
    {
        return User::findOrFail(self::ACTIVE_USER_CID);
    }
}
