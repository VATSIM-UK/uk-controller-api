<?php

namespace App;

use App\Models\User\User;

abstract class BaseFilamentTestCase extends BaseFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::findOrFail(self::ACTIVE_USER_CID), 'web');
    }
}
