<?php
namespace App;

use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class BaseFunctionalTestCase extends BaseTestCase
{
    use DatabaseTransactions;

    private static $hasSeeded = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (!self::$hasSeeded) {
            self::$hasSeeded = true;
            shell_exec('php artisan db:seed');
        }
    }
}
