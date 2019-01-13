<?php

namespace App\Models\User;

use App\BaseFunctionalTestCase;

class UserStatusTest extends BaseFunctionalTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(UserStatus::class, new UserStatus());
    }

    public function testItCanSayIfItsActive()
    {
        $this->assertTrue(UserStatus::find(1)->active);
    }

    public function testItCanSayIfItsBanned()
    {
        $this->assertTrue(UserStatus::find(2)->banned);
    }

    public function testItCanSayIfItsDisabled()
    {
        $this->assertTrue(UserStatus::find(3)->disabled);
    }
}
