<?php

namespace App\Providers;

use App\Acars\Provider\AcarsProviderInterface;
use App\Acars\Provider\HoppieAcarsProvider;
use Illuminate\Support\ServiceProvider;

class AcarsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->singleton(AcarsProviderInterface::class, HoppieAcarsProvider::class);
    }
}
