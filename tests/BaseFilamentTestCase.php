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
        $user = User::findOrFail(self::ACTIVE_USER_CID);
        $user->roles()->sync([Role::idFromKey(RoleKeys::WEB_TEAM)]);
        $this->actingAs(User::findOrFail(self::ACTIVE_USER_CID), 'web');
    }
}
