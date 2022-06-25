<?php

namespace App;

use App\Models\User\Admin;

abstract class BaseFilamentTestCase extends BaseFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(Admin::findOrFail(self::ACTIVE_USER_CID), 'web_admin');
    }
}
