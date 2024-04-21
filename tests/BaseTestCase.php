<?php

namespace App;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;

abstract class BaseTestCase extends TestCase
{
    public const ACTIVE_USER_CID = 1203533;
    public const BANNED_USER_CID = 1203534;
    public const DISABLED_USER_CID = 1203535;
}
