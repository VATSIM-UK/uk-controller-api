<?php

namespace App;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\ParallelTesting;

abstract class BaseFunctionalTestCase extends BaseTestCase
{
    use DatabaseTransactions;

    private static bool $hasSeeded = false;

    protected function setUp(): void
    {
        parent::setUp();

        // In parallel mode, database setup is handled via ParallelTesting hooks.
        if (ParallelTesting::token() !== false) {
            return;
        }

        // For non-parallel runs (e.g. ./vendor/bin/phpunit), keep a once-per-process fallback.
        if (! self::$hasSeeded) {
            self::$hasSeeded = true;

            Artisan::call('passport:keys', ['--force' => true, '--no-interaction' => true]);
            Artisan::call('migrate:fresh', ['--force' => true, '--no-interaction' => true]);
            Artisan::call('db:seed', ['--no-interaction' => true]);

            Artisan::call('passport:client', [
                '--personal' => true,
                '--name' => 'Test Personal Access Client',
                '--no-interaction' => true,
            ]);
        }
    }
}
