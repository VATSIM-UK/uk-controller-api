<?php

namespace App\Providers;

use App\Services\Acars\AcarsProviderInterface;
use App\Services\Acars\DummyAcarsProvider;
use App\Services\Acars\HoppieAcarsProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * @codeCoverageIgnore 
 */
class AcarsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->register(AcarsProviderInterface::class, function (Application $application) {
            return config('acars.enabled')
                ? $this->app->make(HoppieAcarsProvider::class)
                : $this->app->make(DummyAcarsProvider::class);
        });
    }

    public function provides()
    {
        return [AcarsProviderInterface::class];
    }
}
