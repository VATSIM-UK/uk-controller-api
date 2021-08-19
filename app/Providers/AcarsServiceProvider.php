<?php

namespace App\Providers;

use App\Services\Acars\AcarsProviderInterface;
use App\Services\Acars\DummyAcarsProvider;
use App\Services\Acars\HoppieAcarsProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * @codeCoverageIgnore
 */
class AcarsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->bind(
            AcarsProviderInterface::class,
            config('acars.enabled') ? HoppieAcarsProvider::class : DummyAcarsProvider::class
        );
    }

    public function provides()
    {
        return [AcarsProviderInterface::class];
    }
}
