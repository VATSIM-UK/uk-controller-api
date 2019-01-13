<?php
namespace App;

use Laravel\Lumen\Testing\TestCase;

abstract class BaseTestCase extends TestCase
{
    const ACTIVE_USER_CID = 1203533;
    const BANNED_USER_CID = 1203534;
    const DISABLED_USER_CID = 1203535;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Returns the root of the tests folder.
     */
    public function getTestRoot()
    {
        return base_path() . '/tests';
    }
}
