<?php

namespace App;

use App\Models\User\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class BaseFilamentTestCase extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory)
    }
}
