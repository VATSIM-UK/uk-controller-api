<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\ServiceProvider;

class ParallelTestingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->environment('testing')) {
            return;
        }

        /**
         * Runs once per parallel worker database.
         *
         * Notes for this repo:
         * - We run migrations + seed per worker so each process gets an isolated DB state.
         * - Passport needs a Personal Access Client in each worker database.
         * - We intentionally do NOT generate Passport keys here (file race). Generate them once
         *   in CI (passport:keys --force) and in non-parallel local runs (see BaseFunctionalTestCase).
         */
        ParallelTesting::setUpTestDatabase(function (string $token): void {
            Artisan::call('migrate:fresh', ['--force' => true, '--no-interaction' => true]);
            Artisan::call('db:seed', ['--no-interaction' => true]);

            Artisan::call('passport:client', [
                '--personal' => true,
                '--name' => "Test Personal Access Client ({$token})",
                '--no-interaction' => true,
            ]);
        });
    }
}
